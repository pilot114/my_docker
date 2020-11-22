function sum(a: number, b: number): number {
    return a + b
}

interface IPosition {
    x: number | undefined
    y: number | undefined
}

interface IPositionDefault extends IPosition {
    default: string
}

function position(): IPosition
function position(a: number): IPositionDefault
function position(a: number, b: number): IPosition

function position(a?: number, b?: number) {
    if(!a && !b) {
        return {x:undefined, y: undefined}
    }

    if(!a && b) {
        return {x:undefined, y: a.toString()}
    }

    return {x: a, y: b}
}

console.log(position());
console.log(position(42));
console.log(position(10, 15));
