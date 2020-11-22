interface IRect {
    readonly id: string
    color?: string
    size: {
        width: number
        height: number
    }
}

const rect: IRect = {
    id: '1',
    size: {
        width: 10,
        height: 10,
    },
}
rect.color = 'black'

// явное приведение объекта к типу. есть 2 варианта:
const rect2 = {} as IRect
const rect3 = <IRect>{}

interface IRectWithArea extends IRect {
    getArea(): number
}

const rect4: IRectWithArea = {
    id: '123',
    size: {
        width: 10,
        height: 10,
    },
    getArea(): number {
        return this.size.height * this.size.weight
    }
}

// ===========================

interface IClock {
    time: Date
    setTime(date: Date): void
}

class Clock implements IClock {
    time: Date = new Date()
    setTime(date: Date): void {
        this.time = date
    }
}

// ===========================
// динамическое объявление набора свойств
interface Styles {
    [key: string]: string
}

const css: Styles = {
    border: '1px solid black',
    marginTop: '2px',
}