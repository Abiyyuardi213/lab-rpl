@php
    $pages = [
        ['url' => url('/'), 'lastmod' => now()->toDateString(), 'freq' => 'daily', 'priority' => '1.0'],
        ['url' => url('/tentang'), 'freq' => 'monthly', 'priority' => '0.8'],
        ['url' => url('/praktikum'), 'freq' => 'weekly', 'priority' => '0.9'],
        ['url' => url('/aslab'), 'freq' => 'monthly', 'priority' => '0.7'],
        ['url' => url('/struktur-organisasi'), 'freq' => 'monthly', 'priority' => '0.6'],
        ['url' => url('/pengumuman'), 'freq' => 'daily', 'priority' => '0.9'],
        ['url' => url('/kegiatan'), 'freq' => 'weekly', 'priority' => '0.8'],
    ];
@endphp
{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($pages as $page)
        <url>
            <loc>{{ $page['url'] }}</loc>
            @if(isset($page['lastmod']))
                <lastmod>{{ $page['lastmod'] }}</lastmod>
            @endif
            <changefreq>{{ $page['freq'] }}</changefreq>
            <priority>{{ $page['priority'] }}</priority>
        </url>
    @endforeach
</urlset>