<div class="admin-header">
    <h1 style="font-size: 1.6rem;">Admin</h1>
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="btn btn-secondary">Log Out</button>
    </form>
</div>

<div class="admin-tabs">
    <a href="{{ route('admin.posts.index') }}" class="btn {{ $active === 'blog' ? 'btn-primary' : 'btn-secondary' }}">Blog Posts</a>
    <a href="{{ route('admin.pricing.index') }}" class="btn {{ $active === 'pricing' ? 'btn-primary' : 'btn-secondary' }}">Pricing</a>
    <a href="{{ route('admin.theme.index') }}" class="btn {{ $active === 'theme' ? 'btn-primary' : 'btn-secondary' }}">Theme</a>
    <a href="{{ route('admin.branding.index') }}" class="btn {{ $active === 'branding' ? 'btn-primary' : 'btn-secondary' }}">Branding</a>
</div>
