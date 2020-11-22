function strip(x: string | number) {
    if (typeof x === 'number') {
        return x.toFixed(2)
    }
    return x.trim()
}

class MyResponse {
    header = 'qwe'
    result = 'asd'
}
class MyError {
    header = 'qwe'
    message = 'asd'
}

function handle(res: MyResponse | MyError) {
    if(res instanceof MyResponse) {
        return {
            info: res.header + res.result
        }
    } else {
        return {
            info: res.header + res.message
        }
    }
}

// ===================================

type AlertType = 'success' | 'error' | 'warning'

function setAlertType(type: AlertType) {
    // ...
}

setAlertType('error')