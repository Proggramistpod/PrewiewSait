// Размеры окна и переменные
let board;
let bWidth = 500;
let bHeight = 500;
let context;

// Ракетка
let pWidth = 80;
let pHeight = 10;
let pX = 25; // скорость ракетки

let player = {
    x: 210,
    y: 480,
    width: pWidth,
    height: pHeight,
    vX: pX
};

// Мяч
let ballWidth = 10;
let ballHeight = 10;
let ballVX = 2;
let ballVY = 3;

let ball = {
    x: 250,
    y: 250,
    width: ballWidth,
    height: ballHeight,
    vX: ballVX,
    vY: ballVY
};

let brickWidth = 50;
let brickHeight = 20;
let brickColumns = 8;
let brickRows = 3;
let brickMaxRows = 10;
let brickOffsetLeft = 25;
let brickOffsetTop = 50;
let brickPadding = 10;
let bricks = [];
let bricksCount = 0;

// Счёт и состояние игры
let score = 0;
let gameOver = false;

// Создание кирпичей
function createBricks() {
    bricks = [];
    for (let c = 0; c < brickColumns; c++) {
        bricks[c] = [];
        for (let r = 0; r < brickRows; r++) {
            bricks[c][r] = {
                x: c * (brickWidth + brickPadding) + brickOffsetLeft,
                y: r * (brickHeight + brickPadding) + brickOffsetTop,
                width: brickWidth,
                height: brickHeight,
                status: 1
            };
        }
    }
    bricksCount = brickColumns * brickRows;
}

function drawBricks() {
    for (let c = 0; c < brickColumns; c++) {
        for (let r = 0; r < brickRows; r++) {
            if (bricks[c][r].status === 1) {
                context.fillStyle = "#FFC000";
                context.fillRect(bricks[c][r].x, bricks[c][r].y, brickWidth, brickHeight);
                context.strokeStyle = "#353746";
                context.strokeRect(bricks[c][r].x, bricks[c][r].y, brickWidth, brickHeight);
            }
        }
    }
}
function sendScoreToServer(score) {
    if (score <= 0) return;

    let formData = new FormData();
    formData.append('score', score);

    fetch('save_score.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log('✅ Ответ сервера:', data);
        // alert(data); // раскомментируй для теста
    })
    .catch(error => {
        console.error('❌ Ошибка при отправке:', error);
    });
}

function collisionRect(a, b) {
    return a.x < b.x + b.width &&
           a.x + a.width > b.x &&
           a.y < b.y + b.height &&
           a.y + a.height > b.y;
}

function handleBrickCollision() {
    for (let c = 0; c < brickColumns; c++) {
        for (let r = 0; r < brickRows; r++) {
            let brick = bricks[c][r];
            if (brick.status === 1 && collisionRect(ball, brick)) {
                score++;
                brick.status = 0;
                bricksCount--;

                // Определяем направление отскока
                if (ball.y + ball.height - ball.vY <= brick.y || 
                    ball.y - ball.vY >= brick.y + brick.height) {
                    ball.vY = -ball.vY;
                } else {
                    ball.vX = -ball.vX;
                }
                break;
            }
        }
    }
}

function drawScore() {
    context.fillStyle = "skyblue";
    context.font = "20px sans-serif";
    context.fillText("Счёт: " + score, 10, 25);
}

function movePlayer(e) {
    if (gameOver) {
        if (e.code === "Space") resetGame();
        return;
    }

    if (e.code === "KeyA") {
        let nextX = player.x - player.vX;
        if (nextX >= 0) player.x = nextX;
    } else if (e.code === "KeyD") {
        let nextX = player.x + player.vX;
        if (nextX + player.width <= bWidth) player.x = nextX;
    }
}

function resetGame() {
    gameOver = false;
    score = 0;
    brickRows = 3;
    player.x = 210;
    ball.x = 250;
    ball.y = 250;
    ball.vX = ballVX;
    ball.vY = ballVY;
    createBricks();
}

function update() {
    if (gameOver) {
        context.fillStyle = "red";
        context.font = "20px sans-serif";
        context.fillText("Game Over. Нажмите ПРОБЕЛ для рестарта", 50, 250);
        requestAnimationFrame(update);
        return;
    }

    context.clearRect(0, 0, bWidth, bHeight);
    context.fillStyle = "black";
    context.fillRect(0, 0, bWidth, bHeight);

    ball.x += ball.vX;
    ball.y += ball.vY;

    if (ball.x <= 0 || ball.x + ball.width >= bWidth) {
        ball.vX = -ball.vX;
    }
    if (ball.y <= 0) {
        ball.vY = -ball.vY;
    }

    if (collisionRect(ball, player)) {
        let hitPos = (ball.x + ball.width/2) - (player.x + player.width/2);
        let maxVX = 4;
        ball.vX = hitPos * 0.1;
        if (ball.vX > maxVX) ball.vX = maxVX;
        if (ball.vX < -maxVX) ball.vX = -maxVX;
        ball.vY = -Math.abs(ball.vY);
    }

    if (ball.y + ball.height >= bHeight) {
        gameOver = true;
        sendScoreToServer(score);           // ← Отправляем счёт на сервер
        requestAnimationFrame(update);
        return;
    }

    context.fillStyle = "orange";
    context.fillRect(player.x, player.y, player.width, player.height);

    context.fillStyle = "white";
    context.fillRect(ball.x, ball.y, ball.width, ball.height);

    // Кирпичи
    handleBrickCollision();
    drawBricks();

    // Следующий уровень
    if (bricksCount === 0) {
        brickRows = Math.min(brickRows + 1, brickMaxRows);
        createBricks();
    }

    drawScore();

    requestAnimationFrame(update);
}

window.onload = function() {
    board = document.getElementById("board");
    board.height = bHeight;
    board.width = bWidth;
    context = board.getContext("2d");

    createBricks();
    document.addEventListener("keydown", movePlayer);
    update();
};