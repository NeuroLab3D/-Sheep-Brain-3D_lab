<?php
// api.php
header('Content-Type: application/json; charset=utf-8');

// 🔴 کلید API خود را اینجا قرار دهید (کلید جدیدی که بعد از حذف کلید قبلی ساختید)
$apiKey = 'sk-proj-...'; 

$data = json_decode(file_get_contents('php://input'), true);
$userMessage = $data['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['error' => 'پیامی ارسال نشد.']);
    exit;
}

// تنظیمات هوش مصنوعی (متن سیستم پرامپت را متناسب با سایت خود تغییر دهید)
$postData = [
    'model' => 'gpt-4o-mini', 
    'messages' => [
        ['role' => 'system', 'content' => 'شما دستیار هوشمند و پشتیبان سایت "آزمایشگاه زیست‌شناسی" هستید. شما به دانش‌آموزان و معلمان در مورد تشریح مغز گوسفند، بخش‌های مختلف مغز (مثل مخ، مخچه، بصل‌النخاع، تالاموس و...) و نحوه کار با این سایت کمک می‌کنید. پاسخ‌های شما باید علمی، دقیق، کوتاه و به زبان فارسی روان باشد.'],
        ['role' => 'user', 'content' => $userMessage]
    ],
    'temperature' => 0.7
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);

// ⚠️ اگر هاست شما در ایران است، برای عبور از تحریم باید خط زیر را فعال کنید (پروکسی سرور خود را وارد کنید)
// curl_setopt($ch, CURLOPT_PROXY, 'IP_PROXY:PORT'); 

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(['error' => 'خطا در ارتباط با هوش مصنوعی. (بررسی کنید VPN/پروکسی سرور فعال باشد و حساب شارژ باشد)']);
    exit;
}

$result = json_decode($response, true);
$reply = $result['choices'][0]['message']['content'] ?? 'متاسفانه پاسخی پیدا نکردم.';

echo json_encode(['reply' => $reply]);
?>
