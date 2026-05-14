<aside class="admin-sidebar">
    <div style="padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <p style="color: var(--color-gold); font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase;">Espace</p>
        <h3 style="color: var(--color-white); font-size: 1.5rem;">Administrateur</h3>
    </div>

    <nav class="admin-nav">
        <button @click="currentTab = 'overview'" :class="['admin-nav-btn', { active: currentTab === 'overview' }]">
            <i class="fas fa-home"></i> <span>Vue d'ensemble</span>
        </button>
        <button @click="currentTab = 'candidatures'" :class="['admin-nav-btn', { active: currentTab === 'candidatures' }]">
            <i class="fas fa-user-plus"></i> <span>Candidatures</span>
            <span v-if="pendingArtists.length > 0" class="notif-badge">{{ pendingArtists.length }}</span>
        </button>
        <button @click="currentTab = 'commandes'" :class="['admin-nav-btn', { active: currentTab === 'commandes' }]">
            <i class="fas fa-shopping-cart"></i> <span>Ventes & Revenus</span>
        </button>
        <button @click="currentTab = 'demandes'" :class="['admin-nav-btn', { active: currentTab === 'demandes' }]">
            <i class="fas fa-columns"></i> <span>Kanban (Leads)</span>
        </button>
        <button @click="currentTab = 'oeuvres'" :class="['admin-nav-btn', { active: currentTab === 'oeuvres' }]">
            <i class="fas fa-palette"></i> <span>Inventaire Œuvres</span>
        </button>
        <button @click="currentTab = 'artistes'" :class="['admin-nav-btn', { active: currentTab === 'artistes' }]">
            <i class="fas fa-users"></i> <span>Gestion Artistes</span>
        </button>
        <button @click="currentTab = 'journal'" :class="['admin-nav-btn', { active: currentTab === 'journal' }]">
            <i class="fas fa-newspaper"></i> <span>Gestion Journal</span>
        </button>

        <button @click="currentTab = 'ateliers'" :class="['admin-nav-btn', { active: currentTab === 'ateliers' }]">
            <i class="fas fa-paint-brush"></i> <span>Ateliers</span>
        </button>

        <button @click="currentTab = 'evenements'" :class="['admin-nav-btn', { active: currentTab === 'evenements' }]">
            <i class="fas fa-paint-brush"></i> <span>Événements</span>
        </button>
        
        <button @click="currentTab = 'messages'" :class="['admin-nav-btn', { active: currentTab === 'messages' }]">
            <i class="fas fa-envelope"></i> <span>Messages</span>
            <span v-if="unreadMessagesCount > 0" class="notif-badge">{{ unreadMessagesCount }}</span>
        </button>
        
        <div style="margin-top: auto; display: flex; flex-direction: column; gap: 10px;">
            <button @click="logout" class="admin-nav-btn" style="color: #ff4d4d;">
                <i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span>
            </button>
        </div>
    </nav>
</aside>