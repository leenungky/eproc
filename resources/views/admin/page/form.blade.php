<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('homepage.name')}}</label>
    <div class="col-9">
        <input id="id" name="id" type="hidden">
        <select id="page_id" name="page_id" required class="form-control form-control-sm">
            @foreach($pages as $page)
            <option value="{{$page->id}}">{{$page->name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('homepage.language')}}</label>
    <div class="col-9">
        <input id="language" name="language" placeholder="{{__('homepage.language')}}" type="text" required class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('homepage.title')}}</label>
    <div class="col-9">
        <input id="title" name="title" placeholder="{{__('homepage.title')}}" type="text" required class="form-control form-control-sm">
    </div>
</div>
<div class="form-group row mb-2">
    <label for="text" class="col-3 col-form-label text-right">{{__('homepage.content')}}</label>
    <div class="col-9">
        <textarea id="content" name="content" placeholder="{{__('homepage.content')}}" class="summernote form-control form-control-sm" required></textarea>
    </div>
</div>
