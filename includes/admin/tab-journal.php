<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div><h1 class="admin-header-title">Gestion Journal</h1></div>
    <button @click="showNewsModal = true" class="btn-primary" style="padding: 10px 20px; font-size: 0.8rem;">
        <i class="fas fa-plus"></i> Nouvel Article
    </button>
</div>
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Aperçu</th>
                <th>Titre</th>
                <th>Catégories</th>
                <th style="text-align: center;">À la Une</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="news in allNews" :key="news.id">
                <td style="font-size: 0.85rem; color: #aaa;">{{ formatDate(news.created_at) }}</td>
                <td><img :src="news.image_url" class="admin-thumbnail" style="object-fit: cover;"></td>
                <td><strong style="color: white;">{{ news.titre }}</strong></td>
                <td>{{ news.categorie ? news.categorie.split(', ').join(' • ') : '' }}</td>
                <td style="text-align: center;">
                    <button @click="toggleFeaturedNews(news.id, news.is_featured)" class="action-btn">
                        <i :class="news.is_featured ? 'fas fa-star' : 'far fa-star'" :style="{ color: news.is_featured ? 'var(--color-gold)' : '#666', fontSize: '1.2rem' }"></i>
                    </button>
                </td>
                <td>
                    <button @click="editNews(news)" class="action-btn"><i class="fas fa-edit"></i></button>
                    <button @click="askDelete('news', news.id)" class="action-btn delete"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
        </tbody>
    </table>
</div>