const express = require('express');
const http = require('http');
const WebSocket = require('ws');
const { v4: uuidv4 } = require('uuid');

const app = express();
const server = http.createServer(app);
const wss = new WebSocket.Server({ server });

let qrCodes = {}; // Lưu trữ mã QR và trạng thái của chúng

// Middleware để phục vụ file tĩnh
app.use(express.static('public'));

// API tạo mã QR
app.post('/generate', (req, res) => {
    const qr = uuidv4(); // Tạo mã QR ngẫu nhiên
    const url = `http://localhost:3000/qr/${qr}`; // URL dành riêng cho mã QR

    qrCodes[qr] = { used: false, url }; // Lưu mã QR và trạng thái
    broadcast({ qr, url }); // Gửi mã mới tới tất cả client

    res.json({ qr, url });
});

// API xử lý khi người dùng quét mã QR
app.get('/qr/:code', (req, res) => {
    const code = req.params.code;

    // Kiểm tra mã QR có tồn tại và chưa được sử dụng
    if (qrCodes[code] && !qrCodes[code].used) {
        qrCodes[code].used = true; // Đánh dấu mã QR đã sử dụng

        // Gửi thông báo qua WebSocket tới các client
        broadcast({ usedQR: code });

        res.send(`<h1>Mã QR hợp lệ!</h1><p>Mã ${code} đã được sử dụng.</p>`);
    } else {
        res.send('<h1>Mã QR không hợp lệ hoặc đã được sử dụng.</h1>');
    }
});

// Gửi dữ liệu qua WebSocket
function broadcast(data) {
    wss.clients.forEach(client => {
        if (client.readyState === WebSocket.OPEN) {
            client.send(JSON.stringify(data));
        }
    });
}

// Lắng nghe WebSocket kết nối
wss.on('connection', (ws) => {
    // Gửi mã QR mới nhất và trạng thái khi có kết nối
    const activeQR = Object.keys(qrCodes).find(qr => !qrCodes[qr].used);
    if (activeQR) {
        ws.send(JSON.stringify({ qr: activeQR, url: qrCodes[activeQR].url }));
    }
});

server.listen(3000, () => {
    console.log('Server chạy tại http://localhost:3000');
});
