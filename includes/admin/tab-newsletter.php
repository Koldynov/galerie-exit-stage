<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 class="admin-header-title">Abonnés Newsletter</h1>
        <p style="color: #888; font-size: 0.9rem;">Gérez votre base de contacts pour vos campagnes d'emailing.</p>
    </div>
    <div style="display: flex; gap: 15px;">
        <button @click="openNewsletterComposer('all')" class="btn-primary" style="padding: 10px 20px; font-size: 0.8rem;" :disabled="subscribersList.length === 0">
            <i class="fas fa-paper-plane"></i> Nouvelle Campagne
        </button>
        <button @click="exportCSV" class="btn-secondary" style="padding: 10px 20px; font-size: 0.8rem; border-color: #27ae60; color: #27ae60;" :disabled="subscribersList.length === 0">
            <i class="fas fa-file-csv"></i> Exporter
        </button>
    </div>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Email</th>
                <th>Date d'inscription</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="sub in subscribersList" :key="sub.id">
                <td><strong style="color: white;">{{ sub.email }}</strong></td>
                <td style="color: #aaa;"><i class="far fa-calendar-alt"></i> {{ formatDate(sub.created_at) }}</td>
                <td style="text-align: right;">
                    <button @click="openNewsletterComposer(sub.email)" class="btn-icon" style="color: var(--color-gold); margin: 0;" title="Envoyer un email direct">
                        <i class="fas fa-envelope"></i>
                    </button>
                </td>
            </tr>
            <tr v-if="subscribersList.length === 0">
                <td colspan="3" style="text-align:center; padding: 40px; color: #888;">Aucun abonné pour le moment.</td>
            </tr>
        </tbody>
    </table>
</div>

<teleport to="body">
    <div v-if="showNewsletterModal" @click.self="closeNewsletterModal" style="z-index: 999999; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.8); display: flex; justify-content: center; align-items: center; backdrop-filter: blur(4px);">
        
        <div style="background: #0a0a0a; border: 1px solid var(--color-gold); padding: 30px; border-radius: 6px; width: 90%; max-width: 500px; position: relative; box-shadow: 0 15px 40px rgba(0,0,0,0.8); animation: slideUp 0.3s ease-out;">
            
            <button @click="closeNewsletterModal" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: #888; font-size: 1.2rem; cursor: pointer; transition: 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#888'">
                <i class="fas fa-times"></i>
            </button>

            <h2 style="color: white; font-size: 1.4rem; margin: 0 0 20px 0; font-weight: 500;">
                {{ newsletterTarget === 'all' ? 'Campagne Email (Tous)' : 'Email à ' + newsletterTarget.split('@')[0] }}
            </h2>

            <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <p style="color: var(--color-gold); font-size: 0.85rem; font-weight: bold; margin: 0 0 5px 0;">Destinataire(s) :</p>
                <p style="color: #ddd; font-size: 0.95rem; margin: 0;">
                    {{ newsletterTarget === 'all' ? 'Tous les abonnés de la Newsletter (' + subscribersList.length + ')' : newsletterTarget }}
                </p>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="color: white; font-size: 0.9rem; display: block; margin-bottom: 8px;">Sujet de l'email</label>
                <input type="text" v-model="newsletterSubject" placeholder="Sujet..." style="width: 100%; background: #111; border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 4px; color: white; outline: none; font-size: 0.95rem; transition: border-color 0.3s;" onfocus="this.style.borderColor='#d4af37'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="color: white; font-size: 0.9rem; display: block; margin-bottom: 8px;">Votre message (Sera envoyé par email)</label>
                <textarea v-model="newsletterBody" rows="6" placeholder="Bonjour..." style="width: 100%; background: #111; border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 4px; color: white; outline: none; resize: vertical; font-family: inherit; font-size: 0.95rem; transition: border-color 0.3s;" onfocus="this.style.borderColor='#d4af37'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'"></textarea>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="color: white; font-size: 0.9rem; display: block; margin-bottom: 8px;">Image jointe (Optionnel)</label>
                <input type="file" accept="image/*" @change="handleNewsletterAttachment" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px dashed rgba(212, 175, 55, 0.5); padding: 12px; border-radius: 4px; color: white; outline: none; cursor: pointer; font-size: 0.9rem;">
                <p v-if="newsletterAttachmentName" style="font-size: 0.85rem; color: var(--color-gold); margin-top: 8px;">
                    <i class="fas fa-file-image"></i> {{ newsletterAttachmentName }}
                </p>
            </div>

            <div v-if="newsletterProgress.total > 0" style="margin-bottom: 20px;">
                <p style="color: #aaa; font-size: 0.85rem; margin-bottom: 8px;">Envoi en cours : {{ newsletterProgress.current }} / {{ newsletterProgress.total }}</p>
                <div style="width: 100%; background: #222; border-radius: 4px; height: 6px; overflow: hidden;">
                    <div :style="{ width: (newsletterProgress.current / newsletterProgress.total * 100) + '%', background: 'var(--color-gold)', height: '100%', transition: 'width 0.3s' }"></div>
                </div>
            </div>

            <button @click="sendNewsletter" style="width: 100%; padding: 12px; background: transparent; border: 1px solid var(--color-gold); color: var(--color-gold); font-weight: bold; letter-spacing: 1px; text-transform: uppercase; border-radius: 4px; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.background='var(--color-gold)'; this.style.color='#000';" onmouseout="this.style.background='transparent'; this.style.color='var(--color-gold)';" :disabled="isSendingNewsletter">
                <span v-if="isSendingNewsletter"><i class="fas fa-spinner fa-spin"></i> ENVOI EN COURS...</span>
                <span v-else>ENVOYER L'EMAIL</span>
            </button>
            
        </div>
    </div>
</teleport>