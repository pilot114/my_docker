// есть часть стандартной библиотеки, которая импортируется сама, она называется prelude
// https://doc.rust-lang.org/std/prelude/index.html

use std::{
    // для сравнения через match
    cmp::Ordering,
    // библиотека ввода/вывода
    io::stdin,
};
// типаж Rng из библиотеки рандомизации, для работы с генератором
use rand::{
    thread_rng,
    Rng
};

fn main() {
    println!("Guess the number!");

    // получаем генератор, локальный для текущего потока выполнения
    let secret_number = thread_rng().gen_range(1..100);

    loop {
        println!("Please input your guess.");
        // let - переменная, mut - изменяемая
        // String - фрагмент текста в кодировке UTF-8
        // new - Ассоциированная функция — это функция, реализованная для типа
        let mut guess = String::new();

        stdin()
            // ссылки нужны, чтобы переиспользовать память
            // так как ссылки тоже неизменяемы, нужно явно прописать mut
            .read_line(&mut guess)
            // возращается тип Result, являющийся enum (задано перечисление возможных значений)
            // expect завершает выполнение, если Result это Error, иначе возвращает данные из Result
            .expect("Failed to read line");

        // затенение - переиспользование переменной, но с другим типом
        // parse использует аннотирование переменной для выбора типа (: u32)
        let guess: u32 = match guess.trim().parse() {
            Ok(num) => num,
            // _ - означает любой тип
            Err(_) => continue,
        };

        // клешни краба для подстановки значений =)
        println!("You guessed: {guess}");

        // match состоит из "мапы" шаблонов на куски кода
        match guess.cmp(&secret_number) {
            Ordering::Less => println!("Too small!"),
            Ordering::Greater => println!("Too big!"),
            Ordering::Equal => {
                println!("You win!");
                break;
            }
        }
    }
}
