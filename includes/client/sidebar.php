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

        <div class="sidebar-footer">
            <button @click="handleLogout" class="client-nav-btn logout-btn">
                <i class="fas fa-sign-out-alt"></i> Se déconnecter
            </button>
        </div>
    </nav>
</aside>