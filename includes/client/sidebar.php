<aside class="client-sidebar">
    <div class="sidebar-header">
        <div class="avatar-circle">
            {{ userInitials }}
        </div>
        <h3 style="color: white; margin-bottom: 5px;">{{ displayFullName }}</h3>
        <p style="color: #888; font-size: 0.8rem; word-break: break-all;">{{ currentUser.email }}</p>
    </div>

    <nav class="client-nav">
        <button @click="currentTab = 'profil'" :class="['client-nav-btn', { active: currentTab === 'profil' }]">
            <i class="far fa-id-badge"></i> Mon Profil
        </button>
        <button @click="currentTab = 'favoris'" :class="['client-nav-btn', { active: currentTab === 'favoris' }]">
            <i class="far fa-heart"></i> Mes Favoris
            <span v-if="favorisList.length > 0" class="badge gold-badge">{{ favorisList.length }}</span>
        </button>

        <button @click="currentTab = 'artistes-suivis'" :class="['client-nav-btn', { active: currentTab === 'artistes-suivis' }]">
            <i class="fas fa-users"></i> Mes Artistes
            <span v-if="artistesSuivis.length > 0" class="badge gold-badge">{{ artistesSuivis.length }}</span>
        </button>

        <button @click="currentTab = 'demandes'" :class="['client-nav-btn', { active: currentTab === 'demandes' }]">
            <i class="far fa-envelope"></i> Mes Demandes
        </button>
        <button @click="currentTab = 'achats'" :class="['client-nav-btn', { active: currentTab === 'achats' }]">
            <i class="fas fa-shopping-bag"></i> Mes Achats
        </button>

        <div v-if="isArtist || isAdmin" style="margin-top: 20px; padding-top: 20px; border-top: 1px dashed rgba(255,255,255,0.1);">
            <h4 style="color: #888; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; padding-left: 15px;">Accès Réservé</h4>
            
            <a v-if="isArtist" href="studio" class="client-nav-btn" style="text-decoration: none; color: var(--color-gold);">
                <i class="fas fa-palette"></i> Mon Studio
            </a>
            
            <a v-if="isAdmin" href="dashboard" class="client-nav-btn" style="text-decoration: none; color: #4da8da;">
                <i class="fas fa-cogs"></i> Espace Admin
            </a>
        </div>

        <div class="sidebar-footer" style="margin-top: auto; padding-top: 20px;">
            <button @click="handleLogout" class="client-nav-btn logout-btn">
                <i class="fas fa-sign-out-alt"></i> Se déconnecter
            </button>
        </div>
    </nav>
</aside>