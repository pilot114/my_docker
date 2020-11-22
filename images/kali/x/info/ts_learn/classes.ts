class TypeScript {
    version: string

    constructor(version: string) {
        this.version = version
    }

    info(name: string): string {
        return `[${name}] TS version is ${this.version}`
    }
}

class Car {
    readonly countWheel: number = 4

    constructor(readonly model: string) {
    }
}

// =============================
class Animal {
    protected voice: string = ''
    public color: string = 'black'

    private go() {
        console.log('GO');
    }
}

class Cat extends Animal {
    public setVoice(voice: string): void {
        this.voice = voice
    }
}

const cat = new Cat()
cat.setVoice('test')
console.log(cat.color);

// ==================================

// можно наследоваться, но сам ни во что не компилируется
abstract class Component {
    abstract render(): void
    abstract info(): string
}

