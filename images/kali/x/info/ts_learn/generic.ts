const arrayOfNumbers: Array<number> = [1,2,3]
const arrayOfStrings: Array<string> = ['hello', 'world']

function reverse<T>(array: T[]) {
    return array.reverse()
}

reverse(arrayOfNumbers)
reverse(arrayOfStrings)