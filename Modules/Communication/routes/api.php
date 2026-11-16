<?php

use Illuminate\Support\Facades\Route;
use Modules\Communication\Http\Controllers\CommunicationController;

Route::middleware('auth:sanctum')->prefix('communication')->name('communication.')->group(function () {
    Route::get('/messages', [CommunicationController::class, 'messages'])->name('messages.index');
    Route::post('/messages', [CommunicationController::class, 'storeMessage'])->name('messages.store');
    Route::get('/messages/{id}', [CommunicationController::class, 'showMessage'])->name('messages.show');
    Route::put('/messages/{id}', [CommunicationController::class, 'updateMessage'])->name('messages.update');
    Route::delete('/messages/{id}', [CommunicationController::class, 'destroyMessage'])->name('messages.destroy');

    Route::get('/notifications', [CommunicationController::class, 'notifications'])->name('notifications.index');
    Route::patch('/notifications/{notificationId}/read', [CommunicationController::class, 'markNotificationRead'])->name('notifications.read');

    Route::get('/contacts', [CommunicationController::class, 'contacts'])->name('contacts');
    Route::get('/roles', [CommunicationController::class, 'roles'])->name('roles');
    Route::get('/conversations', [CommunicationController::class, 'conversations'])->name('conversations.index');
    Route::post('/conversations', [CommunicationController::class, 'startConversation'])->name('conversations.store');
    Route::get('/conversations/{conversationId}/messages', [CommunicationController::class, 'conversationMessages'])->name('conversations.messages');
    Route::post('/conversations/{conversationId}/messages', [CommunicationController::class, 'replyToConversation'])->name('conversations.reply');
    Route::patch('/conversations/{conversationId}/archive', [CommunicationController::class, 'archiveConversation'])->name('conversations.archive');
    Route::get('/conversations/{conversationId}/messages/{messageId}/attachment', [CommunicationController::class, 'attachment'])->name('conversations.attachment');

    Route::get('/portal', [CommunicationController::class, 'portal'])->name('portal');
});
