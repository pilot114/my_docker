package main

import (
    "database/sql"
    _ "github.com/mattn/go-sqlite3"
    "github.com/gin-gonic/gin"
    "strconv"
    "net/http"
    "errors"
)

func checkErr(err error) {
    if err != nil {
        panic(err)
    }
}

type Sqlite struct {
    Db *sql.DB
}

func newSqlite(path string) *Sqlite {
    m := new(Sqlite)
    db, err := sql.Open("sqlite3", path)
    checkErr(err)
    m.Db = db
    return m
}

func (wrapper *Sqlite) CreateTaskTableIfNotExists() sql.Result {
    s := "CREATE TABLE if not exists task(id integer primary key autoincrement, value text, name text, created text, updated text, status integer)"
    stmt, err := wrapper.Db.Prepare(s)
    checkErr(err)
    res, err := stmt.Exec()
    checkErr(err)
    return res
}

func (wrapper *Sqlite) CreateTask(value string, name string) int64 {
    stmt, err := wrapper.Db.Prepare("INSERT INTO task(value, name, created, updated, status) values(?, ?, datetime('now'), datetime('now'), 1)")
    checkErr(err)
    res, err := stmt.Exec(value, name)
    checkErr(err)
    id, err := res.LastInsertId()
    checkErr(err)
    return id
}

func (wrapper *Sqlite) UpdateStatus(id int64, status int) int64 {
    stmt, err := wrapper.Db.Prepare("update task set status=?,updated=datetime('now') where id=?")
    checkErr(err)
    res, err := stmt.Exec(status, id)
    checkErr(err)
    affect, err := res.RowsAffected()
    checkErr(err)
    return affect
}

type TaskRow struct {
    Id int64 `json:"id"`
    Value string `json:"value"`
    Name string `json:"name"`
    Created string `json:"created"`
    Updated string `json:"updated"`
    Status int `json:"status"`
}
type Task struct {
    Value string `json:"value"`
    Name  string `json:"name"`
}
type TaskStatus struct {
    Status int `json:"status"`
}


func (wrapper *Sqlite) GetTasks() []TaskRow {
    rows, err := wrapper.Db.Query("SELECT * FROM task")
    defer rows.Close()
    checkErr(err)

    rs := make([]TaskRow, 0)
    for rows.Next() {
        r := new(TaskRow)
        err = rows.Scan(&r.Id, &r.Value, &r.Name, &r.Created, &r.Updated, &r.Status)
        checkErr(err)
        rs = append(rs, *r)
    }
    rows.Close()
    return rs
}

func (wrapper *Sqlite) GetFreeTask(taskName string) (TaskRow, error) {
    rows, err := wrapper.Db.Query("SELECT * FROM task where status = ? and name = ?", 1, taskName)
    defer rows.Close()
    checkErr(err)

    r := new(TaskRow)

    rows.Next()
    err = rows.Scan(&r.Id, &r.Value, &r.Name, &r.Created, &r.Updated, &r.Status)
    rows.Close()

    if err != nil {
        return *r, errors.New("not found")
    }

    return *r, nil
}

func (wrapper *Sqlite) DeleteTask(id int64) int64 {
    stmt, err := wrapper.Db.Prepare("delete from task where id=?")
    checkErr(err)
    res, err := stmt.Exec(id)
    defer stmt.Close()
    checkErr(err)
    affect, err := res.RowsAffected()
    checkErr(err)
    return affect
}

func main() {
    db := newSqlite("../data/dealer.db")
    // создаем таблицу задач, если не существует
    db.CreateTaskTableIfNotExists()

    gin.SetMode(gin.ReleaseMode)
    r := gin.Default()

    // список задач
    r.GET("/task", func(c *gin.Context) {

        rows := db.GetTasks()

        c.JSON(200, gin.H{
            "items": rows,
        })
    })

    // добавляем задачу
    r.POST("/task", func(c *gin.Context) {
        var task Task
        if err := c.ShouldBindJSON(&task); err != nil {
            c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
            return
        }

        id := db.CreateTask(task.Value, task.Name)

        c.JSON(200, gin.H{
            "id": id,
        })
    })

    // обновляем статус задачи
    r.POST("/task/:id", func(c *gin.Context) {
        id, err := strconv.ParseInt(c.Param("id"), 10, 64)
        checkErr(err)

        var task TaskStatus
        if err := c.ShouldBindJSON(&task); err != nil {
            c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
            return
        }

        db.UpdateStatus(id, task.Status)

        c.JSON(200, gin.H{
            "status": true,
        })
    })

    //  удаляем задачу
    r.DELETE("/task/:id", func(c *gin.Context) {
        id, err := strconv.ParseInt(c.Param("id"), 10, 64)
        checkErr(err)

        db.DeleteTask(id)

        c.JSON(200, gin.H{
            "status": true,
        })
    })

    // доп.метод (не REST) - берём задачу в работу
    r.GET("/task/start", func(c *gin.Context) {
        taskName := c.Query("name")

        task, err := db.GetFreeTask(taskName)
        if err != nil {
            c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
            return
        }

        db.UpdateStatus(task.Id, 2)

        c.JSON(200, gin.H{
            "task": task,
        })
    })

    r.Run()
    db.Db.Close()
}
