<?php include("config/db.php"); ?>
<?php include("includes/header.php"); ?>

<style>
/* ---------------- HOME PAGE STYLE ---------------- */
.hero-banner{width:100%;height:360px;background:#e9e5e0 url('assets/images/hero.svg') center/cover no-repeat;position:relative;display:flex;align-items:center;justify-content:center;margin-bottom:50px;}
.hero-banner::after{content:"";position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,.15),rgba(0,0,0,.25));mix-blend-mode:multiply;}
.hero-content{position:relative;color:#fff;text-align:center;}
.hero-content h1{font-size:46px;letter-spacing:4px;margin:0 0 12px;font-weight:600;}
.hero-content a{display:inline-block;background:#fff;color:#111;padding:10px 26px;font-size:13px;letter-spacing:1px;text-transform:uppercase;border-radius:2px;text-decoration:none;transition:.3s;}
.hero-content a:hover{background:#111;color:#fff;}

.section{margin:0 auto 70px;max-width:1320px;padding:0 30px;}
.section h2{font-size:32px;margin:0 0 28px;font-weight:600;letter-spacing:1px;}

/* Spotlight grid (Hàng mới) */
.spotlight-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:18px;align-items:start;margin-top:18px;}
.spotlight-grid .card{border-radius:6px;overflow:hidden;transition:transform .18s ease, box-shadow .18s ease;border:1px solid #eee;background:#fff}
.spotlight-grid .card:hover{transform:translateY(-10px);box-shadow:0 14px 30px rgba(18,22,26,0.08)}
.spotlight-grid .card img{width:100%;height:260px;object-fit:cover;display:block;background:#f5f5f5}
.spotlight-grid .card h3{font-size:14px;margin:10px 12px 6px;line-height:1.25;min-height:36px}
.spotlight-grid .card .meta{padding:0 12px 14px;text-align:center}
.spotlight-grid .card .meta .title{font-size:14px;color:#222;margin-bottom:6px}
.spotlight-grid .card .meta .price{font-size:13px;color:#111;font-weight:700}
.spotlight-title{display:flex;align-items:center;justify-content:space-between;gap:12px}

/* Horizontal carousel */
.carousel-wrapper{position:relative;overflow:hidden;}
.carousel-track{display:flex;gap:26px;will-change:transform;transition:transform .65s cubic-bezier(.22,.61,.36,1);align-items:stretch}
.carousel-track.dragging{transition:none;}
.card{flex:0 0 220px;background:#fff;border:1px solid #eee;border-radius:4px;overflow:hidden;display:flex;flex-direction:column;justify-content:flex-start;}
.card img{width:100%;height:200px;object-fit:cover;display:block;}
.card h3{font-size:13px;font-weight:600;margin:10px 12px 4px;letter-spacing:.5px;}
.card p{margin:0 12px 12px;font-size:12px;color:#555;}
.card a.more{margin:0 12px 14px;font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#222;text-decoration:none;}
.card a.more:hover{text-decoration:underline;}
.carousel-btn{position:absolute;top:50%;transform:translateY(-50%);width:34px;height:34px;border:1px solid #ddd;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 3px 10px rgba(0,0,0,.08);z-index:10;transition:.3s;} 
.carousel-btn:hover{background:#111;color:#fff;border-color:#111;} 
.carousel-btn.left{left:8px;} 
.carousel-btn.right{right:8px;} 

.inspire .card{flex:0 0 calc((100% - 78px)/4);border:none;border-radius:10px;position:relative;overflow:hidden;aspect-ratio:3/4;height:auto}
.inspire .card img{width:100%;height:100%;border-bottom:0;border-radius:10px;display:block;object-fit:cover}
/* ensure the media container fills the card and images cover it */
.inspire .inspire-media{height:100%;display:block}
.inspire .inspire-media img{width:100%;height:100%;object-fit:cover;display:block}
.inspire .card h3{display:none}

/* responsive column counts for inspire: 4 / 3 / 2 / 1 */
@media(max-width:1200px){
    .inspire .card{flex:0 0 calc((100% - 52px)/3)} /* 3 columns, 2 gaps */
}
@media(max-width:900px){
    .inspire .card{flex:0 0 calc((100% - 26px)/2)} /* 2 columns, 1 gap */
}
@media(max-width:640px){
    .inspire .card{flex:0 0 100%} /* 1 column */
}

.inspire .inspire-media{position:relative;display:block}
.inspire .inspire-media::before{content:'';position:absolute;inset:0;background:rgba(0,0,0,0);transition:background .18s;pointer-events:none;z-index:2}
.inspire .inspire-media img{position:relative;z-index:1}
.inspire .insta-badge{position:absolute;left:12px;bottom:12px;background:rgba(0,0,0,0.65);color:#fff;padding:6px 10px;border-radius:8px;font-size:13px;opacity:0;transform:translateY(8px);transition:opacity .18s, transform .18s;z-index:3}
/* Show badge and darken when hovered OR focused (keyboard users) */
.inspire .card:hover .insta-badge,
.inspire .card:focus-within .insta-badge{opacity:1;transform:translateY(0)}
.inspire .card:hover img,
.inspire .card:focus-within img{transform:scale(1.03);transition:transform .25s}
.inspire .card:hover .inspire-media::before,
.inspire .card:focus-within .inspire-media::before{background:rgba(0,0,0,0.22)}

/* make links fill the card so hovering the card area triggers anchor hover */
.inspire .inspire-link{display:block;height:100%;width:100%}

/* Make .card outline visible for keyboard focus (better accessibility) */
.inspire .card:focus-within{outline:2px solid rgba(0,0,0,0.06);outline-offset:4px}

.see-more{display:block;text-align:center;margin:34px 0 0;font-size:13px;text-transform:uppercase;letter-spacing:1px;text-decoration:none;color:#222;}
.see-more:hover{text-decoration:underline;}

@media(max-width:900px){.hero-banner{height:300px;} .hero-content h1{font-size:36px;} .card{flex:0 0 180px;} .inspire .card{flex:0 0 200px;} }
@media(max-width:640px){.hero-banner{height:240px;} .hero-content h1{font-size:28px;letter-spacing:2px;} .section h2{font-size:26px;} .carousel-btn{display:none;} }
</style>

<!-- HERO -->
<div class="hero-banner">
    <div class="hero-content">
        <a href="collection.php">Khám Phá Bộ Sưu Tập</a>
    </div>
</div>

<?php
// Lấy nhiều sản phẩm cho các khối khác nhau
$products = [];
// Truy vấn sản phẩm với kiểm tra lỗi
$res = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 18");
if(!$res){
    echo '<div style="max-width:1320px;margin:0 auto;padding:20px;color:#b00;font-size:14px;">Lỗi truy vấn sản phẩm: '.htmlspecialchars($conn->error).'</div>';
} else {
    while($r = $res->fetch_assoc()){ $products[] = $r; }
}
$spotlight = array_slice($products,0,6);
$inspire = array_slice($products,6,6);
// Sample inspirations fallback (6 items). Put corresponding images in assets/images/
$inspirations = [
    ['name'=>'ChloeNguyen',  'instagram'=>'bychloenguyen',     'image'=>'camhungthoitrang/chloe.jpg'],
    ['name'=>'Châu Bùi',   'instagram'=>'chaubui_',      'image'=>'camhungthoitrang/chaubuii.jpg'],
    ['name'=>'Quỳnh Anh Shyn',       'instagram'=>'quynhanhshyn_',     'image'=>'camhungthoitrang/quynhanh.jpg'],
    ['name'=>'Oanh',      'instagram'=>'oanhdaqueen', 'image'=>'camhungthoitrang/oanh.jpg'],
    ['name'=>'Lưu Hải', 'instagram'=>'lil_ocean__',       'image'=>'camhungthoitrang/luuhai.jpg'],
    ['name'=>'Tikka Hoàng Hiền',     'instagram'=>'tikkaisweird',         'image'=>'camhungthoitrang/tikkai.jpg'],
    ['name'=>'Lyly',     'instagram'=>'may__lily',         'image'=>'camhungthoitrang/phuongly.jpg'],
];

// If database didn't provide inspiration items, use the sample list
if(empty($inspire)){
    $inspire = $inspirations;
}
?>
<?php
// Helper trả về đường dẫn ảnh sản phẩm hoặc placeholder nếu trống/không tồn tại
function productImage($item, $placeholder){
    if(!empty($item['image'])){
        $name = $item['image'];
        $uploadPath = __DIR__ . '/uploads/' . $name;
        $legacyPath = __DIR__ . '/assets/images/' . $name; // hỗ trợ ảnh cũ lưu trong assets/images
        if(is_file($uploadPath)){
            return 'uploads/' . htmlspecialchars($name);
        } elseif(is_file($legacyPath)) {
            return 'assets/images/' . htmlspecialchars($name);
        }
    }
    return $placeholder;
}
?>

<!-- SPOTLIGHT SECTION -->
<section class="section" id="spotlight">
    <div class="spotlight-title">
        <h2>Hàng mới</h2>
        <a class="see-more" href="collection.php">Xem Thêm</a>
    </div>
    <div class="spotlight-grid">
        <?php if(empty($spotlight)): ?>
            <?php for($i=1;$i<=6;$i++): ?>
                <div class="card">
                    <img src="assets/images/placeholder-bag.svg" alt="Placeholder" loading="lazy">
                    <div class="meta">
                        <div class="title">Sản phẩm mẫu <?= $i ?></div>
                        <div class="price">0₫</div>
                    </div>
                </div>
            <?php endfor; ?>
        <?php else: ?>
            <?php foreach($spotlight as $item): ?>
                <div class="card">
                    <img src="<?= productImage($item,'assets/images/placeholder-bag.svg') ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                    <div class="meta">
                        <div class="title"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="price"><?= number_format($item['price'], 0, ',', '.') ?>₫</div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- FASHION INSPIRATION -->
<section class="section inspire" id="inspiration" style="background:#f5f3f0;padding-top:40px;padding-bottom:50px;">
    <h2 style="text-align:center;margin-bottom:36px;">Cảm hứng thời trang</h2>
    <div class="carousel-wrapper">
        <button class="carousel-btn left" data-target="inspireTrack" aria-label="Prev">&#10094;</button>
        <div class="carousel-track" id="inspireTrack">
            <?php if(empty($inspire)): ?>
                <?php for($i=1;$i<=6;$i++): ?>
                    <div class="card">
                        <img src="assets/images/placeholder-inspire.svg" alt="Placeholder" loading="lazy">
                    </div>
                <?php endfor; ?>
            <?php else: ?>
                <?php foreach($inspire as $item): ?>
                    <?php
                        // determine instagram handle if present in item
                        $instaHandle = '';
                        if(!empty($item['instagram'])) $instaHandle = $item['instagram'];
                        elseif(!empty($item['insta'])) $instaHandle = $item['insta'];
                        elseif(!empty($item['instagram_handle'])) $instaHandle = $item['instagram_handle'];
                        // fallback: try to use name as handle (not prefixed)
                        if(!$instaHandle && !empty($item['name'])){
                            // do not auto-create handle; keep empty
                        }
                        $instaUrl = $instaHandle ? 'https://instagram.com/'.ltrim($instaHandle,'@') : '#';
                    ?>
                            <?php if($instaHandle): ?>
                                <div class="card">
                                    <a class="inspire-link" href="<?= $instaUrl ?>" target="_blank" rel="noopener noreferrer" aria-label="Open Instagram @<?= htmlspecialchars(ltrim($instaHandle,'@')) ?>">
                                        <div class="inspire-media">
                                            <?php // fix: pass a proper placeholder file (not a directory)
                                            $imgSrc = productImage($item,'assets/images/placeholder-inspire.svg'); ?>
                                            <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($item['name'] ?? 'Inspire') ?>" loading="lazy">
                                            <div class="insta-badge">@<?= htmlspecialchars(ltrim($instaHandle,'@')) ?></div>
                                        </div>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="card" tabindex="0">
                                    <div class="inspire-media">
                                        <?php $imgSrc = productImage($item,'assets/images/placeholder-inspire.svg'); ?>
                                        <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($item['name'] ?? 'Inspire') ?>" loading="lazy">
                                        <div class="insta-badge"><?= htmlspecialchars($item['name'] ?? '') ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button class="carousel-btn right" data-target="inspireTrack" aria-label="Next">&#10095;</button>
    </div>
    <a class="see-more" href="collection.php">Xem Thêm</a>
</section>

<script>
// Smooth transform-based carousel
function initCarousel(trackId){
    const track = document.getElementById(trackId);
    if(!track) return;
    const cards = Array.from(track.children);
    let index = 0;
    const gap = 26;
    // compute step dynamically because widths may not be available at script-run time
    function getStep(){
        const first = cards[0];
        if(!first) return 0;
        let w = first.getBoundingClientRect().width || first.offsetWidth || 0;
        // fallback: if width is still zero, try average
        if(!w){
            const total = cards.reduce((s,c)=>s + (c.offsetWidth||0),0);
            w = total ? Math.round(total / cards.length) : 0;
        }
        return w + gap;
    }

    // compute how many cards fit in the visible container (page size)
    function getVisibleCount(){
        const step = getStep();
        if(!step) return 1;
        const containerWidth = track.getBoundingClientRect().width || track.offsetWidth || 0;
        return Math.max(1, Math.floor(containerWidth / step));
    }

    function clampIndex(i){
        const visible = getVisibleCount();
        const maxIndex = Math.max(0, cards.length - visible);
        if(i < 0) return 0;
        if(i > maxIndex) return maxIndex;
        return i;
    }

    function updateControls(){
        const visible = getVisibleCount();
        const maxIndex = Math.max(0, cards.length - visible);
        document.querySelectorAll(`.carousel-btn[data-target='${trackId}']`).forEach(btn=>{
            if(btn.classList.contains('left')){
                const disabled = index <= 0;
                btn.disabled = disabled;
                btn.setAttribute('aria-disabled', disabled ? 'true' : 'false');
            } else {
                const disabled = index >= maxIndex;
                btn.disabled = disabled;
                btn.setAttribute('aria-disabled', disabled ? 'true' : 'false');
            }
        });
    }

    // move by a page (number of visible cards); if there aren't enough remaining,
    // jump to the last page so final items align neatly.
    function move(dir){
        const visible = getVisibleCount();
        let newIndex = index + dir * visible;
        newIndex = clampIndex(newIndex);
        index = newIndex;
        const step = getStep();
        const translate = -index * step;
        track.style.transform = `translateX(${translate}px)`;
        updateControls();
    }

    // attach buttons
    document.querySelectorAll(`.carousel-btn[data-target='${trackId}']`).forEach(btn=>{
        btn.addEventListener('click',()=> move(btn.classList.contains('left')?-1:1));
    });

    // ensure controls correct on resize and at initialization
    window.addEventListener('resize', ()=>{
        // clamp index in case visibleCount changed
        index = clampIndex(index);
        const step = getStep();
        track.style.transform = `translateX(${-index*step}px)`;
        updateControls();
    });
    updateControls();
    // optional drag interaction (pointer-based)
    let startX = 0; let current = 0; let dragging=false; let pointerId = null;
    track.addEventListener('pointerdown', e => {
        dragging = true;
        startX = e.clientX;
        current = index;
        pointerId = e.pointerId;
        track.classList.add('dragging');
        try{ track.setPointerCapture(pointerId); }catch(_){/* ignore */}
        e.preventDefault();
    });
    track.addEventListener('pointermove', e => {
        if(!dragging) return;
        const dx = e.clientX - startX;
        const step = getStep();
        track.style.transform = `translateX(${-current*step + dx}px)`;
    });
    function endDrag(e){
        if(!dragging) return;
        dragging = false;
        track.classList.remove('dragging');
        const dx = (e.clientX || 0) - startX;
        const step = getStep();
        if(Math.abs(dx) > (step/3)){
            index = current + (dx < 0 ? 1 : -1);
            if(index < 0) index = 0;
            if(index > cards.length - 1) index = cards.length - 1;
        }
        track.style.transform = `translateX(${-index*step}px)`;
        try{ if(pointerId) track.releasePointerCapture(pointerId); }catch(_){/* ignore */}
        pointerId = null;
    }
    track.addEventListener('pointerup', endDrag);
    track.addEventListener('pointercancel', endDrag);
    track.addEventListener('pointerleave', endDrag);
}
initCarousel('spotlightTrack');
initCarousel('inspireTrack');
</script>
</script>
<script>
// Ensure inspire cards have equal portrait height (width * 4/3)
function updateInspireCardHeights(){
    try{
        const cards = Array.from(document.querySelectorAll('.inspire .card'));
        if(!cards.length) return;
        cards.forEach(card=>{ card.style.height = 'auto'; });
        // compute each card width (they may be different across breakpoints)
        cards.forEach(card=>{
            const w = card.getBoundingClientRect().width || card.offsetWidth;
            if(!w) return;
            const h = Math.round(w * (4/3)); // aspect 3/4 => height = width * 4/3
            card.style.height = h + 'px';
        });
    }catch(e){console.warn('updateInspireCardHeights error', e)}
}
window.addEventListener('load', updateInspireCardHeights);
window.addEventListener('resize', ()=>{ window.requestAnimationFrame(updateInspireCardHeights); });
// also run after images load (in case image loading affects layout)
document.querySelectorAll('.inspire img').forEach(img=>{
    if(img.complete) return;
    img.addEventListener('load', ()=> window.requestAnimationFrame(updateInspireCardHeights));
});
</script>
</script>

<?php include("includes/footer.php"); ?>
