<?php

declare(strict_types=1);

final class Mailer
{
    public static function send(string $to, string $subject, string $body): bool
    {
        $from = Settings::get('email', 'noreply@localhost');
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . Settings::get('restaurant_name', 'Restaurant') . ' <' . $from . '>',
            'Reply-To: ' . $from,
        ];
        return @mail($to, $subject, $body, implode("\r\n", $headers));
    }

    public static function reservationConfirmation(array $reservation): void
    {
        $name = Settings::get('restaurant_name');
        $subject = "Reservation Request Received - {$name}";
        $body = self::wrapTemplate(
            '<h2>Thank you, ' . e($reservation['full_name']) . '!</h2>
            <p>We have received your reservation request and will confirm shortly.</p>
            <ul>
                <li><strong>Date:</strong> ' . e(format_date($reservation['reservation_date'])) . '</li>
                <li><strong>Time:</strong> ' . e(format_time($reservation['reservation_time'])) . '</li>
                <li><strong>Guests:</strong> ' . (int) $reservation['num_guests'] . '</li>
            </ul>
            <p>Status: <strong>Pending confirmation</strong></p>'
        );
        self::send($reservation['email'], $subject, $body);

        $notify = Settings::get('notification_email');
        if ($notify !== '') {
            $adminSubject = "New Reservation - {$reservation['full_name']}";
            $adminBody = self::wrapTemplate(
                '<h2>New reservation</h2>
                <p><strong>Name:</strong> ' . e($reservation['full_name']) . '</p>
                <p><strong>Email:</strong> ' . e($reservation['email']) . '</p>
                <p><strong>Phone:</strong> ' . e($reservation['phone_number'] ?? '') . '</p>
                <p><strong>Date:</strong> ' . e($reservation['reservation_date']) . ' at ' . e($reservation['reservation_time']) . '</p>
                <p><strong>Guests:</strong> ' . (int) $reservation['num_guests'] . '</p>
                <p><strong>Requests:</strong> ' . e($reservation['special_requests'] ?? '') . '</p>'
            );
            self::send($notify, $adminSubject, $adminBody);
        }
    }

    public static function reservationStatusUpdate(array $reservation): void
    {
        $status = ucfirst($reservation['status']);
        $subject = "Reservation {$status} - " . Settings::get('restaurant_name');
        $body = self::wrapTemplate(
            '<h2>Hello, ' . e($reservation['full_name']) . '</h2>
            <p>Your reservation status has been updated to: <strong>' . e($status) . '</strong></p>
            <ul>
                <li><strong>Date:</strong> ' . e(format_date($reservation['reservation_date'])) . '</li>
                <li><strong>Time:</strong> ' . e(format_time($reservation['reservation_time'])) . '</li>
                <li><strong>Guests:</strong> ' . (int) $reservation['num_guests'] . '</li>
            </ul>'
        );
        self::send($reservation['email'], $subject, $body);
    }

    private static function wrapTemplate(string $content): string
    {
        $name = Settings::get('restaurant_name');
        return '<!DOCTYPE html><html><body style="font-family:sans-serif;color:#333;max-width:600px;margin:0 auto;">
            <div style="background:#78350f;color:#fff;padding:20px;text-align:center;"><h1 style="margin:0;">' . e($name) . '</h1></div>
            <div style="padding:24px;">' . $content . '</div>
            </body></html>';
    }
}
