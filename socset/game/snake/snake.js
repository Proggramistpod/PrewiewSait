// Размер ячейки в пикселях
const blockSize = 25;
const rows = 20;
const cols = 30;

let board;
let context;

// Переменные змейки
let snakeX;
let snakeY;
let velocityX = 0;
let velocityY = 0;
let snakeBody = [];

// Переменные еды
let foodX;
let foodY;

// Счёт и окончание игры
let score = 0;
let gameOver = false;

window.onload = function() {
    board = document.getElementById("board");
    board.height = rows * blockSize;
    board.width = cols * blockSize;
    context = board.getContext("2d");

    placeFood();
    placeSnake();

    document.addEventListener("keyup", changeDirection);
    setInterval(update, 100);
};

function update() {
    if (gameOver) {
        resetGame();
        return;
    }

    // Очистка поля
    context.fillStyle = "black";
    context.fillRect(0, 0, board.width, board.height);

    // Еда
    context.fillStyle = "red";
    context.fillRect(foodX, foodY, blockSize, blockSize);

    // Проверка съедания
    if (snakeX === foodX && snakeY === foodY) {
        snakeBody.push([foodX, foodY]);
        score++;
        placeFood();
    }

    // Движение тела змейки
    for (let i = snakeBody.length - 1; i > 0; i--) {
        snakeBody[i] = snakeBody[i - 1];
    }
    if (snakeBody.length) {
        snakeBody[0] = [snakeX, snakeY];
    }

    // Движение головы
    snakeX += velocityX * blockSize;
    snakeY += velocityY * blockSize;

    // Проверка выхода за границы
    if (snakeX < 0 || snakeX >= cols * blockSize || snakeY < 0 || snakeY >= rows * blockSize) {
        gameOver = true;
        alert("Игра окончена! Ваш счёт: " + score + "\nНажмите ОК для перезапуска");
        return;
    }

    // Проверка столкновения с собой
    for (let i = 0; i < snakeBody.length; i++) {
        if (snakeX === snakeBody[i][0] && snakeY === snakeBody[i][1]) {
            gameOver = true;
            alert("Игра окончена! Ваш счёт: " + score + "\nНажмите ОК для перезапуска");
            return;
        }
    }

    // Отрисовка головы
    context.fillStyle = "lime";
    context.fillRect(snakeX, snakeY, blockSize, blockSize);

    // Отрисовка тела
    for (let i = 0; i < snakeBody.length; i++) {
        context.fillRect(snakeBody[i][0], snakeBody[i][1], blockSize, blockSize);
    }

    // Счётчик
    context.fillStyle = "lightblue";
    context.font = "20px sans-serif";
    context.fillText("Счёт: " + score, 10, 25);
}

function placeFood() {
    foodX = Math.floor(Math.random() * cols) * blockSize;
    foodY = Math.floor(Math.random() * rows) * blockSize;
}

function placeSnake() {
    snakeX = Math.floor(Math.random() * cols) * blockSize;
    snakeY = Math.floor(Math.random() * rows) * blockSize;
    snakeBody = [];
    velocityX = 0;
    velocityY = 0;
    score = 0;
    gameOver = false;
}

function changeDirection(event) {
    if (event.code === "KeyW" && velocityY !== 1) {
        velocityX = 0;
        velocityY = -1;
    } else if (event.code === "KeyS" && velocityY !== -1) {
        velocityX = 0;
        velocityY = 1;
    } else if (event.code === "KeyA" && velocityX !== 1) {
        velocityX = -1;
        velocityY = 0;
    } else if (event.code === "KeyD" && velocityX !== -1) {
        velocityX = 1;
        velocityY = 0;
    }
}

function resetGame() {
    placeSnake();
    placeFood();
    gameOver = false;
}