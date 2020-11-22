/**
 typescript - js that scales (js, который маштабируется)

 - отлавливает ошибки на этапе разработки
 - рефакторить проще
 - удобнее для команды
 */

// типизация
const isLoading: boolean = false
const int: number = 42
const float: number = 42.2
const message: string = 'hello'

const numArray: number[] = [1,2,3,4]
const numArray2: Array<number> = [1,2,3,4]
const strArray: string[] = ['hello', 'world']

// для статичных массив можно указать разные типы данных
const tuple: [number, string] = [123, 'world']

// любой тип
let any: any = 123

function sayMayName(name: string): void {
    console.log(name)
}

// возвращаемый тип never: дял функций генерящих ошибки или бесконечные циклы
function error(): never {
    throw new Error()
}

// пользовательские типы
type ID = string | number
type User = {
    id: ID,
    name: string
}
