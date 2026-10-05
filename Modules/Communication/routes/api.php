<?php

use Illuminate\Support\Facades\Route;
use Modules\Communication\Http\Controllers\CommunicationController;

Route::middleware(['auth:sanctum'])->prefix('communication')->name('communication.')->group(function () {
    Route::get('/notifications', [CommunicationController::class, 'notifications'])->name('notifications.index');
    Route::patch('/notifications/read-all', [CommunicationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::patch('/notifications/{notificationId}/read', [CommunicationController::class, 'markRead'])->name('notifications.read');
    Route::get('/roles', [CommunicationController::class, 'roles'])->name('roles.index');
    Route::get('/messages', [CommunicationController::class, 'messages'])->name('messages.index');
    Route::post('/messages', [CommunicationController::class, 'send'])->name('messages.store');
    Route::get('/recipients', [CommunicationController::class, 'recipients'])->name('recipients.index');
    Route::get('/conversations', [CommunicationController::class, 'conversations'])->name('conversations.index');
    Route::post('/conversations', [CommunicationController::class, 'startConversation'])->name('conversations.store');
    Route::get('/conversations/{conversationId}/messages', [CommunicationController::class, 'conversationMessages'])->name('conversations.messages.index');
    Route::post('/conversations/{conversationId}/messages', [CommunicationController::class, 'reply'])->name('conversations.messages.store');
    Route::patch('/conversations/{conversationId}/archive', [CommunicationController::class, 'archive'])->name('conversations.archive');
    Route::get('/conversations/{conversationId}/messages/{messageId}/attachment', [CommunicationController::class, 'attachment'])->name('conversations.attachments.show');
});
