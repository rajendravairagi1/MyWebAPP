<div class="section" style="padding-top: 0;">
    <div class="container" style="text-align: center;">
        <div class="pill-row">
            @foreach ([
                ['icon' => 'shield-check', 'label' => 'Bank-grade data security'],
                ['icon' => 'database', 'label' => 'Automatic daily backups'],
                ['icon' => 'rocket', 'label' => 'Live in minutes, no setup calls'],
                ['icon' => 'smartphone', 'label' => 'Works on mobile, no app needed'],
            ] as $badge)
                <span class="pill">
                    <span class="pill-icon-blink">@include('partials.icon', ['name' => $badge['icon'], 'size' => 15])</span>
                    {{ $badge['label'] }}
                </span>
            @endforeach
        </div>
    </div>
</div>
