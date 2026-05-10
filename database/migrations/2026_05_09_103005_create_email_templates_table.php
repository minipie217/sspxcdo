<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('subject');
            $table->longText('body');
            $table->timestamps();
        });

        $now = now();

        DB::table('email_templates')->insert([
            [
                'key'     => 'sponsor_registration_otp',
                'label'   => 'Sponsor Registration — OTP',
                'subject' => 'Your Verification Code',
                'body'    => '<p>Hello <strong>{name}</strong>,</p><p>Use the code below to complete your registration.</p><p><strong>{otp}</strong></p><p>This code expires in 15 minutes.</p><p>If you did not register, you can safely ignore this email.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'     => 'sponsor_login_otp',
                'label'   => 'Sponsor Login — OTP',
                'subject' => 'Your Login Verification Code',
                'body'    => '<p>Hello <strong>{name}</strong>,</p><p>Use the code below to log in.</p><p><strong>{otp}</strong></p><p>This code expires in 15 minutes.</p><p>If you did not request this, you can safely ignore this email.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'     => 'admin_otp',
                'label'   => 'Admin — OTP',
                'subject' => 'Your Admin Verification Code',
                'body'    => '<p>Hello <strong>{name}</strong>,</p><p>Use the code below to log in to the admin panel.</p><p><strong>{otp}</strong></p><p>This code expires in 15 minutes.</p><p>If you did not request this, please ignore this email.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'     => 'reservation_expired',
                'label'   => 'Ticket Reservation Expired',
                'subject' => 'Your Ticket Reservation Has Expired',
                'body'    => '<p>Hello <strong>{name}</strong>,</p><p>Your reservation for ticket <strong>{ticket_number}</strong> has expired.</p><p>The ticket has been released back to the available pool.</p><p>You may reserve it again if it is still available.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'     => 'payment_received',
                'label'   => 'Payment Received — Admin Notification',
                'subject' => 'New Payment Pending Confirmation',
                'body'    => '<p>Hello <strong>{admin_name}</strong>,</p><p>A payment has been submitted for ticket <strong>{ticket_number}</strong>.</p><p>Sponsor: <strong>{sponsor_name}</strong></p><p>Raffle: <strong>{raffle_title}</strong></p><p>Please review and confirm the payment.</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'     => 'payment_confirmed',
                'label'   => 'Payment Confirmed — Sponsor Notification',
                'subject' => 'Payment Confirmed — Ticket Sold!',
                'body'    => '<p>Hello <strong>{name}</strong>,</p><p>Your payment for ticket <strong>{ticket_number}</strong> has been confirmed.</p><p>Raffle: <strong>{raffle_title}</strong></p><p>Draw Date: <strong>{draw_date}</strong></p><p>Good luck!</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'     => 'payment_rejected',
                'label'   => 'Payment Rejected — Sponsor Notification',
                'subject' => 'Payment Rejected',
                'body'    => '<p>Hello <strong>{name}</strong>,</p><p>Your payment for ticket <strong>{ticket_number}</strong> was not confirmed.</p><p>The ticket has been released back to the available pool.</p><p>{rejection_reason}</p>',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};