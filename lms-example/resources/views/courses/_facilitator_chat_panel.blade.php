@php
  $course = $course ?? null;
  $unitId = $unitId ?? (isset($current) ? optional($current)->id : null);
  $isFacilitator = $isFacilitator ?? false;
  if (!$course) return;
  $indexUrl = route('learn.facilitator-chat.index', $course);
  $storeUrl = route('learn.facilitator-chat.store', $course);
  $updateUrlBase = rtrim(url('/learn/' . $course->slug . '/facilitator-chat'), '/');
  $instructorPageUrl = auth()->user() && auth()->user()->canEditCourse($course) ? route('instructor.facilitator-chat', $course) : null;
@endphp
<div id="facilitator-chat-wrap" class="facilitator-chat-wrap" data-index-url="{{ $indexUrl }}" data-store-url="{{ $storeUrl }}" data-update-url-base="{{ $updateUrlBase }}" data-unit-id="{{ $unitId or '' }}" data-is-facilitator="{{ $isFacilitator ? '1' : '0' }}" data-instructor-url="{{ $instructorPageUrl ?? '' }}">
  <button type="button" class="facilitator-chat-toggle" id="facilitator-chat-toggle" aria-label="Open Q&A"><i class="bi bi-chat-dots"></i> Q&A</button>
  <aside class="facilitator-chat-panel" id="facilitator-chat-panel" aria-label="Chat with facilitator">
    <div class="facilitator-chat-header">
      <h2 class="h6 mb-0"><i class="bi bi-chat-left-text me-2"></i>Chat with facilitator</h2>
      @if($instructorPageUrl)
      <a href="{{ $instructorPageUrl }}" target="_blank" rel="noopener" class="btn btn-link btn-sm text-dark">Open in new window</a>
      @endif
      <button type="button" class="btn-close btn-close-sm" id="facilitator-chat-close" aria-label="Close"></button>
    </div>
    @if($unitId)
    <div class="facilitator-chat-filter px-3 py-2 border-bottom">
      <div class="btn-group btn-group-sm">
        <button type="button" class="btn btn-outline-secondary active" data-filter="section">This section</button>
        <button type="button" class="btn btn-outline-secondary" data-filter="all">All Q&A</button>
      </div>
    </div>
    @endif
    <div class="facilitator-chat-list flex-grow-1 overflow-auto p-3" id="facilitator-chat-list">
      <p class="text-muted small text-center py-4">Loading…</p>
    </div>
    <div class="facilitator-chat-forms border-top p-3">
      <div class="facilitator-chat-ask mb-3">
        <label class="form-label small">Ask a question</label>
        <textarea class="form-control form-control-sm" id="facilitator-chat-question" rows="2" placeholder="Type your question…" maxlength="4000"></textarea>
        <button type="button" class="btn btn-danger btn-sm mt-2" id="facilitator-chat-send-q">Send question</button>
      </div>
      @if($isFacilitator)
      <div class="facilitator-chat-announce">
        <label class="form-label small">Post announcement</label>
        <textarea class="form-control form-control-sm" id="facilitator-chat-announce" rows="2" placeholder="Message to all attendees…" maxlength="4000"></textarea>
        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="facilitator-chat-send-announce">Post</button>
      </div>
      @endif
    </div>
  </aside>
</div>
<style>
.facilitator-chat-wrap { --fchat-width: 380px; }
.facilitator-chat-toggle { position: fixed; right: 0; top: 50%; transform: translateY(-50%); z-index: 1040; writing-mode: vertical-rl; text-orientation: mixed; padding: .6rem .5rem; background: var(--lms-accent, #dc3545); color: #fff; border: none; border-radius: 8px 0 0 8px; font-size: .85rem; cursor: pointer; box-shadow: -2px 0 8px rgba(0,0,0,.15); }
.facilitator-chat-toggle:hover { color: #fff; filter: brightness(1.05); }
.facilitator-chat-panel { position: fixed; right: 0; top: 48px; bottom: 0; width: var(--fchat-width); max-width: 100%; background: #fff; box-shadow: -4px 0 20px rgba(0,0,0,.12); z-index: 1035; display: flex; flex-direction: column; transform: translateX(100%); transition: transform .25s ease; }
.facilitator-chat-panel.open { transform: translateX(0); }
.facilitator-chat-panel.open ~ .facilitator-chat-toggle { display: none; }
.facilitator-chat-header { display: flex; align-items: center; justify-content: space-between; gap: .5rem; padding: .75rem 1rem; border-bottom: 1px solid #dee2e6; }
.facilitator-chat-header .btn-close { margin-left: auto; }
.facilitator-chat-list { min-height: 120px; }
.fchat-item { padding: .6rem 0; border-bottom: 1px solid #f0f0f0; }
.fchat-item:last-child { border-bottom: none; }
.fchat-meta { font-size: .75rem; color: #6c757d; margin-bottom: .25rem; }
.fchat-body { font-size: .9rem; white-space: pre-wrap; word-break: break-word; }
.fchat-replies { margin-left: 1rem; margin-top: .5rem; padding-left: .75rem; border-left: 2px solid #dee2e6; }
.fchat-reply { font-size: .85rem; padding: .4rem 0; }
.fchat-status { font-size: .7rem; text-transform: uppercase; }
.fchat-actions { margin-top: .5rem; }
.fchat-actions .btn { font-size: .75rem; padding: .2rem .5rem; }
.fchat-reply-form { margin-top: .5rem; }
.fchat-reply-form textarea { font-size: .85rem; }
</style>
@push('scripts')
<script>
(function(){
  var wrap = document.getElementById('facilitator-chat-wrap');
  if (!wrap) return;
  var panel = document.getElementById('facilitator-chat-panel');
  var toggle = document.getElementById('facilitator-chat-toggle');
  var close = document.getElementById('facilitator-chat-close');
  var list = document.getElementById('facilitator-chat-list');
  var qTa = document.getElementById('facilitator-chat-question');
  var sendQ = document.getElementById('facilitator-chat-send-q');
  var announceTa = document.getElementById('facilitator-chat-announce');
  var sendAnnounce = document.getElementById('facilitator-chat-send-announce');
  var indexUrl = wrap.dataset.indexUrl;
  var storeUrl = wrap.dataset.storeUrl;
  var updateUrlBase = wrap.dataset.updateUrlBase;
  var unitId = wrap.dataset.unitId || null;
  var isFacilitator = wrap.dataset.isFacilitator === '1';
  var pollTimer = null;
  var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

  function getUnitId() {
    var f = wrap.querySelector('[data-filter="section"].active') || wrap.querySelector('.facilitator-chat-filter [data-filter="section"]');
    if (f && f.classList.contains('active')) return unitId || null;
    return null;
  }

  function fetchUrl() {
    var u = getUnitId();
    return indexUrl + (u ? '?unit_id=' + u : '');
  }

  function openPanel() { panel.classList.add('open'); toggle.style.display = 'none'; load(); startPoll(); }
  function closePanel() { panel.classList.remove('open'); toggle.style.display = ''; stopPoll(); }
  toggle.addEventListener('click', openPanel);
  close.addEventListener('click', closePanel);

  wrap.querySelectorAll('[data-filter]').forEach(function(b){
    b.addEventListener('click', function(){ wrap.querySelectorAll('[data-filter]').forEach(function(x){ x.classList.remove('active'); }); this.classList.add('active'); load(); });
  });

  function startPoll() { stopPoll(); pollTimer = setInterval(load, 20000); }
  function stopPoll() { if (pollTimer) { clearInterval(pollTimer); pollTimer = null; } }

  function load() {
    fetch(fetchUrl(), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(r){ return r.json(); })
      .then(function(d){ render(d); })
      .catch(function(){ list.innerHTML = '<p class="text-danger small">Could not load Q&A. Try again.</p>'; });
  }

  function render(d) {
    var canReply = d.can_reply;
    var canAnnounce = d.can_announce;
    var canStatus = d.can_update_status;
    var items = d.items || [];
    if (items.length === 0) {
      list.innerHTML = '<p class="text-muted small text-center py-4">No questions yet. Use the box below to ask.</p>';
      return;
    }
    var html = '';
    items.forEach(function(it){
      html += itemHtml(it, canReply, canStatus);
    });
    list.innerHTML = html;
    list.querySelectorAll('.fchat-do-reply').forEach(function(btn){
      btn.addEventListener('click', function(){ var id = this.dataset.id; var f = list.querySelector('.fchat-reply-form[data-id="'+id+'"]'); if (f) f.style.display = f.style.display === 'none' ? 'block' : 'none'; });
    });
    list.querySelectorAll('.fchat-send-reply').forEach(function(btn){
      btn.addEventListener('click', function(){ var id = this.dataset.id; var ta = list.querySelector('.fchat-reply-form[data-id="'+id+'"] textarea'); if (ta && ta.value.trim()) sendReply(id, ta.value.trim()); });
    });
    list.querySelectorAll('.fchat-set-status').forEach(function(btn){
      btn.addEventListener('click', function(){ var id = this.dataset.id, st = this.dataset.status; if (id && st) setStatus(id, st); });
    });
  }

  function itemHtml(it, canReply, canStatus) {
    var meta = (it.user_name || 'Someone') + ' · ' + (it.unit_title ? it.unit_title : 'General') + ' · ' + (it.created_at ? new Date(it.created_at).toLocaleString() : '');
    var cls = it.type === 'announcement' ? 'fchat-item fchat-announce' : 'fchat-item';
    var status = it.type === 'question' && it.status ? '<span class="fchat-status badge bg-' + (it.status === 'answered' ? 'success' : 'secondary') + '">' + it.status + '</span>' : '';
    var actions = '';
    if (it.type === 'question' && canReply) {
      actions += '<button type="button" class="btn btn-link btn-sm fchat-do-reply" data-id="'+it.id+'">Reply</button> ';
    }
    if (it.type === 'question' && canStatus && it.status === 'pending') {
      actions += '<button type="button" class="btn btn-link btn-sm fchat-set-status" data-id="'+it.id+'" data-status="answered">Mark answered</button> <button type="button" class="btn btn-link btn-sm fchat-set-status" data-id="'+it.id+'" data-status="dismissed">Dismiss</button>';
    }
    var replyForm = '';
    if (it.type === 'question' && canReply) {
      replyForm = '<div class="fchat-reply-form" data-id="'+it.id+'" style="display:none"><textarea class="form-control form-control-sm mb-1" rows="2" placeholder="Your reply…"></textarea><button type="button" class="btn btn-sm btn-danger fchat-send-reply" data-id="'+it.id+'">Send reply</button></div>';
    }
    var replies = (it.replies || []).map(function(r){ return '<div class="fchat-reply"><strong>' + (r.user_name || '') + '</strong>: ' + escapeHtml(r.body) + '</div>'; }).join('');
    return '<div class="'+cls+'"><div class="fchat-meta">'+meta+' '+status+'</div><div class="fchat-body">'+escapeHtml(it.body)+'</div>'+(actions ? '<div class="fchat-actions">'+actions+'</div>'+replyForm : '')+(replies ? '<div class="fchat-replies">'+replies+'</div>' : '')+'</div>';
  }

  function escapeHtml(s){ if (!s) return ''; var d=document.createElement('div'); d.textContent=s; return d.innerHTML; }

  function sendQuestion() {
    if (!qTa || !qTa.value.trim()) return;
    var payload = { body: qTa.value.trim(), type: 'question', unit_id: getUnitId() || undefined, _token: csrf };
    sendQ.disabled = true;
    fetch(storeUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(payload) })
      .then(function(r){ return r.json().then(function(j){ if (!r.ok) throw new Error(j.message || 'Failed'); return j; }); })
      .then(function(){ qTa.value = ''; load(); })
      .catch(function(e){ alert(e.message || 'Could not send.'); })
      .finally(function(){ sendQ.disabled = false; });
  }

  function sendAnnouncement() {
    if (!announceTa || !announceTa.value.trim()) return;
    var payload = { body: announceTa.value.trim(), type: 'announcement', _token: csrf };
    sendAnnounce.disabled = true;
    fetch(storeUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(payload) })
      .then(function(r){ return r.json().then(function(j){ if (!r.ok) throw new Error(j.message || 'Failed'); return j; }); })
      .then(function(){ announceTa.value = ''; load(); })
      .catch(function(e){ alert(e.message || 'Could not post.'); })
      .finally(function(){ sendAnnounce.disabled = false; });
  }

  function sendReply(parentId, body) {
    var payload = { body: body, type: 'reply', in_reply_to_id: parseInt(parentId, 10), _token: csrf };
    fetch(storeUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(payload) })
      .then(function(r){ return r.json().then(function(j){ if (!r.ok) throw new Error(j.message || 'Failed'); return j; }); })
      .then(function(){ load(); })
      .catch(function(e){ alert(e.message || 'Could not send reply.'); });
  }

  function setStatus(id, status) {
    fetch(updateUrlBase + '/' + id, { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ status: status, _token: csrf }) })
      .then(function(r){ return r.json().then(function(j){ if (!r.ok) throw new Error(j.message || 'Failed'); return j; }); })
      .then(function(){ load(); })
      .catch(function(e){ alert(e.message || 'Could not update.'); });
  }

  if (sendQ) sendQ.addEventListener('click', sendQuestion);
  if (sendAnnounce) sendAnnounce.addEventListener('click', sendAnnouncement);
})();
</script>
@endpush
