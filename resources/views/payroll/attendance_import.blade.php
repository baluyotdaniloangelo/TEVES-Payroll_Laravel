@extends('layouts.layout')
@section('content')
<div class="page-wrapper"><div class="content container-fluid">
    <div class="page-header"><h3 class="page-title">Import Attendance</h3>
        <a href="{{ route('employee_attendance_logs') }}">Back to Employee Logs</a>
    </div>
    @if(!$ready)
        <div class="alert alert-info">Attendance import is awaiting database setup. Existing manual attendance is available.</div>
    @else
    <div id="import-message" role="status" aria-live="polite"></div>
    @if($branches->isEmpty())
        <div class="alert alert-warning" role="alert">
            No branches are assigned to this account. Branch access must be configured before importing attendance.
            @if(strcasecmp(trim((string)$data->user_type), 'Admin') === 0)
                <a href="{{ url('/user') }}">Open user management to review branch access.</a>
            @else
                Ask your administrator to assign the branches you handle.
            @endif
        </div>
    @endif
    <div class="card"><div class="card-body">
        <h4>1. Select branch and device</h4>
        <p>Use a separate device profile for each source of employee IDs. Upload the original XLS, XLSX, CSV, or TXT export.</p>
        <form id="import-upload" class="row g-3">
            <div class="col-md-4"><label for="import-branch" class="form-label">Branch</label>
                <select id="import-branch" class="form-select" required @disabled($branches->isEmpty())><option value="">{{ $branches->isEmpty() ? 'No assigned branches' : 'Select branch' }}</option>
                    @foreach($branches as $branch)<option value="{{ $branch->branch_id }}">{{ $branch->branch_code }} — {{ $branch->branch_name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4"><label for="import-device" class="form-label">Device / source profile</label>
                <select id="import-device" class="form-select" required><option value="">Select a branch first</option></select>
            </div>
            <div class="col-md-4"><label for="import-new-device" class="form-label">New device name</label>
                <div class="input-group"><input id="import-new-device" class="form-control" maxlength="100" placeholder="e.g. Front counter biometric">
                    <button type="button" class="btn btn-outline-primary" id="import-add-device">Add device</button></div>
            </div>
            <div class="col-md-8"><label for="import-file" class="form-label">Attendance export</label>
                <input type="file" id="import-file" class="form-control" accept=".xls,.xlsx,.csv,.txt" required>
            </div>
            <div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary" type="submit">Upload and preview</button></div>
        </form>
        <div class="mt-3"><label for="import-history" class="form-label">Resume a recent import</label>
            <select id="import-history" class="form-select"><option value="">Choose an uploaded report</option>
                @foreach($batches as $batch)<option value="{{ $batch->id }}">{{ $batch->created_at }} — {{ $batch->filename }}</option>@endforeach
            </select>
        </div>
    </div></div>
    <section id="import-review" hidden>
        <div class="card"><div class="card-body">
            <h4>2. Match device employees</h4>
            <p>Save mappings before editing the attendance preview. Each mapping is remembered for this branch and device. A name alone does not confirm an employee match.</p>
            <div id="import-mappings" class="table-responsive"></div>
        </div></div>
        <div class="card"><div class="card-body">
            <h4>3. Review attendance</h4>
            <p id="import-summary"></p><a id="import-source" href="#">Download original export</a>
            <p class="mt-2">Check full dates for overnight shifts. Missing punches stay blank. For two punches, enter the break or explicitly choose “No break”. Review regular/overtime classification and shift override before saving.</p>
            <div class="row g-2 my-2">
                <div class="col-md-6"><label for="import-filter" class="form-label">Find employee, device ID, or date</label><input id="import-filter" class="form-control" type="search"></div>
                <div class="col-md-3"><label for="import-status" class="form-label">Status</label><select id="import-status" class="form-select"><option value="">All rows</option><option value="pending">Pending</option><option value="saved">Saved</option><option value="skipped">Skipped</option></select></div>
                <div class="col-md-3 d-flex align-items-end"><button type="button" id="import-save-reviewed" class="btn btn-primary">Save checked rows on this page</button></div>
            </div>
            <div id="import-rows"></div>
            <div class="d-flex gap-3 align-items-center"><button id="import-prev" type="button" class="btn btn-outline-secondary">Previous</button><span id="import-page"></span><button id="import-next" type="button" class="btn btn-outline-secondary">Next</button></div>
        </div></div>
    </section>
    <style>
        .attendance-review-row{border:1px solid #dce1e7;border-radius:6px;padding:16px;margin-bottom:16px}
        .attendance-review-row .form-label{font-size:.875rem}
        .attendance-review-row .raw-punches{white-space:pre-wrap;overflow-wrap:anywhere}
        .attendance-review-row .conflict{background:#fff7e6;padding:10px;border-radius:4px}
    </style>
    <script>
    (() => {
        const base = @json(url('/attendance-import'));
        const token = @json(csrf_token());
        const devices = @json($devices);
        let batch = null, result = null, page = 0, busy = false;
        const size = 25, drafts = new Map();
        const $ = id => document.getElementById(id);
        const node = (tag, text, cls) => { const el = document.createElement(tag); if(text != null)el.textContent=text; if(cls)el.className=cls; return el; };
        const message = (text, error = false) => { const el=$('import-message'); el.className='alert '+(error?'alert-danger':'alert-success'); el.textContent=text; };
        async function api(url, data = null) {
            const options={headers:{'Accept':'application/json','X-CSRF-TOKEN':token},credentials:'same-origin'};
            if(data !== null) { options.method='POST'; if(data instanceof FormData)options.body=data; else { options.headers['Content-Type']='application/json';options.body=JSON.stringify(data); } }
            const response=await fetch(url,options);
            let body; try { body=await response.json(); } catch { throw Error('Request failed. Reload the page and check your login.'); }
            if(!response.ok)throw Error(body.errors?Object.values(body.errors).flat().join(' '):(body.message||'Request failed.'));
            return body;
        }
        function deviceOptions() {
            const list=$('import-device');list.replaceChildren(new Option('Select device',''));
            devices.filter(d=>String(d.branch_idx)===$('import-branch').value).forEach(d=>list.add(new Option(d.name,d.id)));
        }
        $('import-branch').onchange=deviceOptions;
        $('import-add-device').onclick=async()=>{
            try {
                if(!$('import-branch').value || !$('import-new-device').value.trim())throw Error('Select a branch and enter a device name.');
                const device=await api(base+'/devices',{branch_idx:Number($('import-branch').value),name:$('import-new-device').value.trim()});
                if(!devices.some(d=>d.id===device.id))devices.push(device);
                deviceOptions();$('import-device').value=device.id;message('Device profile ready.');
            }catch(e){message(e.message,true);}
        };
        async function load(id) {
            result=await api(base+'/'+id+'/preview');batch=id;drafts.clear();page=0;
            $('import-review').hidden=false;
            $('import-source').href=base+'/'+id+'/source';
            mappings();render();
        }
        $('import-history').onchange=()=>{if($('import-history').value)load($('import-history').value).catch(e=>message(e.message,true));};
        $('import-upload').onsubmit=async event=>{
            event.preventDefault(); if(busy)return;busy=true;const button=event.submitter;button.disabled=true;
            try {
                const form=new FormData();form.append('device_id',$('import-device').value);form.append('file',$('import-file').files[0]);
                const uploaded=await api(base+'/upload',form);await load(uploaded.id);
                $('import-history').add(new Option(result.batch.filename,uploaded.id),1);$('import-history').value=uploaded.id;
                message('Report parsed. Match employees, then review the attendance rows. Nothing has been saved to Employee Logs.');
            }catch(e){message(e.message,true);}finally{busy=false;button.disabled=false;}
        };
        function mappings() {
            const table=node('table',null,'table');const head=node('thead');const tr=node('tr');
            ['Device ID','Name in export','Payroll employee',''].forEach(t=>tr.append(node('th',t)));head.append(tr);table.append(head);
            const body=node('tbody'), unique=new Map();result.rows.forEach(r=>{if(!unique.has(r.external_id))unique.set(r.external_id,r);});
            for(const row of unique.values()) {
                const tr=node('tr');tr.append(node('td',row.external_id),node('td',row.external_name));
                const cell=node('td'), select=node('select',null,'form-select');select.setAttribute('aria-label','Payroll employee for device ID '+row.external_id);select.add(new Option('Select employee',''));
                result.employees.forEach(e=>select.add(new Option(e.number+' — '+e.name,e.id)));select.value=row.employee_idx||'';cell.append(select);tr.append(cell);
                const action=node('td'), button=node('button','Save mapping','btn btn-outline-primary');button.type='button';
                button.onclick=async()=>{button.disabled=true;try {
                    if(!select.value)throw Error('Choose a payroll employee.');
                    if(drafts.size && !window.confirm('Saving this mapping reloads the preview and clears unsaved attendance edits. Continue?'))return;
                    await api(base+'/'+batch+'/mapping',{external_id:row.external_id,employee_idx:Number(select.value)});await load(batch);message('Employee mapping saved for this device.');
                }catch(e){message(e.message,true);}finally{button.disabled=false;}};
                action.append(button);tr.append(action);body.append(tr);
            }
            table.append(body);$('import-mappings').replaceChildren(table);
        }
        function filtered() {
            const text=$('import-filter').value.toLowerCase(),status=$('import-status').value;
            return result.rows.filter(r=>(!status||r.status===status)&&[r.external_id,r.external_name,r.date].join(' ').toLowerCase().includes(text));
        }
        function draft(row) {
            if(!drafts.has(row.id))drafts.set(row.id,{proposed:{...row.proposed},confirmed:false,conflict_action:'keep'});
            return drafts.get(row.id);
        }
        function field(container,label,key,state,type='datetime-local',options=null) {
            const group=node('div',null,'col-md-3');const id='review-'+state.id+'-'+key;
            const lab=node('label',label,'form-label');lab.htmlFor=id;
            const el=node(options?'select':'input',null,options?'form-select':'form-control');el.id=id;
            if(options)options.forEach(([value,text])=>el.add(new Option(text,value)));else el.type=type;
            el.value=state.proposed[key]||'';el.disabled=state.locked;
            el.onchange=()=>{state.proposed[key]=el.value;state.confirmed=false;const cb=$('confirm-'+state.id);if(cb)cb.checked=false;};
            group.append(lab,el);container.append(group);
        }
        function render() {
            const rows=filtered(),pages=Math.max(1,Math.ceil(rows.length/size));page=Math.min(page,pages-1);
            const saved=result.rows.filter(r=>r.status==='saved').length;
            $('import-summary').textContent=result.batch.filename+' • '+result.batch.device+' • '+result.rows.length+' intervals • '+saved+' saved';
            $('import-page').textContent='Page '+(page+1)+' of '+pages+' ('+rows.length+' matching rows)';
            $('import-prev').disabled=page===0;$('import-next').disabled=page>=pages-1;
            const target=$('import-rows');target.replaceChildren();
            rows.slice(page*size,(page+1)*size).forEach(row=>{
                const state=draft(row);state.id=row.id;state.locked=row.status==='saved';
                const card=node('div',null,'attendance-review-row');card.dataset.row=row.id;
                card.append(node('h5',row.date+' — '+row.external_name+' (device '+row.external_id+') — '+row.status));
                const employee=result.employees.find(e=>Number(e.id)===Number(row.employee_idx));
                card.append(node('p',employee?'Payroll employee: '+employee.number+' — '+employee.name:'Employee not mapped. Save a mapping above.'));
                card.append(node('p','Source: '+row.raw.sheet+', row '+row.raw.row+' | Punches: '+row.raw.punches.join(', '),'raw-punches'));
                if(row.warnings.length)card.append(node('p',row.warnings.join(' '),'text-warning'));
                if(row.raw.slots)card.append(node('p','Device slots [In 1, Out 1, In 2, Out 2, OT In, OT Out]: '+row.raw.slots.map(s=>s||'—').join(' / '),'small'));
                const inputs=node('div',null,'row g-2');
                field(inputs,'Time in','log_in',state);field(inputs,'Time out','log_out',state);
                field(inputs,'Break start','breaktime_start',state);field(inputs,'Break end','breaktime_end',state);
                field(inputs,'Break treatment','break_policy',state,'', [['review','Choose break treatment'],['recorded','Recorded / manually entered break'],['none','No break — explicitly confirmed']]);
                field(inputs,'Attendance type','overtime_status',state,'',[['No','Regular / rest day'],['Yes','Overtime']]);
                field(inputs,'Override default shift','override_default_shift',state,'',[['No','No — use employee shift'],['Yes','Yes — disregard shift timing']]);card.append(inputs);
                if(row.conflicts.length) {
                    const box=node('div',null,'conflict mt-3');box.append(node('strong','Existing attendance on this date'));
                    row.conflicts.forEach(c=>box.append(node('p',c.type+': '+c.in+' → '+c.out+'; break '+c.break_start+' → '+c.break_end)));
                    const select=node('select',null,'form-select');select.setAttribute('aria-label','Conflict action');select.add(new Option('Keep existing matching attendance','keep'));select.add(new Option('Replace matching attendance after review','replace'));select.value=state.conflict_action;select.disabled=state.locked;
                    select.onchange=()=>{state.conflict_action=select.value;state.confirmed=false;$('confirm-'+row.id).checked=false;};box.append(select);card.append(box);
                }
                const label=node('label',null,'d-block my-3'), check=node('input');check.type='checkbox';check.id='confirm-'+row.id;check.checked=state.confirmed;check.disabled=state.locked||!row.employee_idx;check.onchange=()=>state.confirmed=check.checked;
                label.append(check,document.createTextNode(' I reviewed the employee, punches, break, dates, and classification.'));card.append(label);
                const actions=node('div',null,'d-flex gap-2'), save=node('button','Save reviewed row','btn btn-primary'), skip=node('button','Skip row','btn btn-outline-secondary');
                save.type=skip.type='button';save.disabled=skip.disabled=state.locked;save.onclick=()=>saveOne(row,card);skip.onclick=()=>saveOne(row,card,'skip');actions.append(save,skip);card.append(actions,node('p','', 'row-message mt-2'));target.append(card);
            });
        }
        async function saveOne(row,card,action='save') {
            const state=draft(row), buttons=card.querySelectorAll('button');buttons.forEach(b=>b.disabled=true);
            const status=card.querySelector('.row-message');status.className='row-message mt-2';
            try {
                if(action==='save'&&!state.confirmed)throw Error('Check the review confirmation first.');
                const response=await api(base+'/'+batch+'/rows/'+row.id,{action,version:row.version,employee_idx:row.employee_idx,confirmed:state.confirmed,proposed:state.proposed,conflict_hash:row.conflict_hash,conflict_action:state.conflict_action});
                status.textContent=response.status==='kept'?'Existing attendance retained.':response.status==='saved'?'Saved to Employee Logs.':'Skipped.';
                if(response.status!=='kept') {row.status=response.status;row.version++;if(response.status==='saved'){card.querySelectorAll('input,select,button').forEach(e=>e.disabled=true);return;}}
            }catch(e){status.className+=' text-danger';status.textContent=e.message;}
            buttons.forEach(b=>b.disabled=false);
        }
        $('import-save-reviewed').onclick=async()=>{
            if(busy)return;busy=true;$('import-save-reviewed').disabled=true;
            try { for(const card of $('import-rows').children) { const row=result.rows.find(r=>String(r.id)===card.dataset.row);if(row.status!=='saved'&&draft(row).confirmed)await saveOne(row,card); } }
            finally{busy=false;$('import-save-reviewed').disabled=false;}
        };
        $('import-filter').oninput=$('import-status').onchange=()=>{page=0;if(result)render();};
        $('import-prev').onclick=()=>{page--;render();};$('import-next').onclick=()=>{page++;render();};
    })();
    </script>
    @endif
</div></div>
@endsection
