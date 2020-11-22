interface Person {
    name: string
    age: number
}

type PersonKeys = keyof Person // может быть только названием поля
let key: PersonKeys = 'name'
key = 'age'

type User = {
    _id: number
    name: string
    email: string
    createdAt: Date
}

// частичный enum по полям типа
let UserKeysNoMeta1 = Exclude<keyof User, '_id' | 'createAt'>
let UserKeysNoMeta2 = Pick<User, 'name' | 'email'>

let u1: UserKeysNoMeta1 = 'name'