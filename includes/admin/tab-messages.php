<div class="admin-tab-header">
    <h2>Boîte de réception</h2>
    <p>Gérez les messages envoyés depuis la page Contact.</p>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th>Sujet</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="msg in allMessages" :key="msg.id">
                <td>{{ formatDateShort(msg.created_at) }}</td>
                <td>
                    <strong>{{ msg.nom }}</strong><br>
                    <span style="font-size:0.8rem; color:#888;">{{ msg.email }}</span>
                </td>
                <td>
                    <span style="color: var(--color-gold); font-size: 0.8rem; text-transform: uppercase;">{{ msg.sujet }}</span><br>
                    {{ msg.message.substring(0, 40) }}...
                </td>
                <td>
                    <span :class="['status-badge', msg.statut === 'nouveau' ? 'new' : 'success']">
                        {{ msg.statut === 'nouveau' ? 'Nouveau' : 'Répondu' }}
                    </span>
                </td>
                <td>
                    <button class="btn-icon" @click="openReplyModal(msg)" title="Répondre">
                        <i class="fas fa-reply"></i>
                    </button>
                </td>
            </tr>
            <tr v-if="allMessages.length === 0">
                <td colspan="5" style="text-align:center; padding: 30px; color: #888;">Aucun message pour le moment.</td>
            </tr>
        </tbody>
    </table>
</div>

<teleport to="body">
    <div v-if="showReplyModal" class="modal-overlay">
        <div class="modal-box" style="max-width: 600px;">
            <div class="modal-header">
                <h3>Répondre à {{ activeMessage.nom }}</h3>
                <button class="btn-close" @click="showReplyModal = false"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                
                <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem;">
                    <p style="color: var(--color-gold); margin-bottom: 5px; font-weight: bold;">Sujet: {{ activeMessage.sujet }}</p>
                    <p style="color: #ccc; line-height: 1.5; white-space: pre-wrap;">{{ activeMessage.message }}</p>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Votre réponse (Sera envoyée par email)</label>
                    <textarea v-model="replyText" rows="6" placeholder="Bonjour, merci pour votre message..." style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 10px; border-radius: 4px; box-sizing: border-box;"></textarea>
                </div>

                <button class="btn-primary" @click="sendContactReply" :disabled="isSendingReply" style="width: 100%;">
                    <span v-if="isSendingReply"><i class="fas fa-spinner fa-spin"></i> Envoi en cours...</span>
                    <span v-else>Envoyer la réponse</span>
                </button>
            </div>
        </div>
    </div>
</teleport>