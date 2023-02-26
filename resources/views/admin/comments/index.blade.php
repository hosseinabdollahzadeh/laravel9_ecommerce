@extends('admin.layouts.admin')

@section('title')
    index comments
@endsection

@section('content')
    <!-- Content Row -->
    <div class="row">

        <div class="col-xl-12 col-md-12 mb-4 p-4 bg-white">
            <div class="mb-4 d-flex flex-column text-center flex-md-row justify-content-md-between">
                <h5 class="font-weight-bold mb-3 mb-md-0">لیست نظرات ({{ $comments->total() }})</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>نام کاربر</th>
                        <th>نام محصول</th>
                        <th>متن</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>

                    </thead>
                    <tbody>
                    @foreach($comments as $key=>$comment)
                        <tr>
                            <th>{{$comments->firstItem() + $key}}</th>
                            <th>{{$comment->user->name ?? $comment->user->cellphone}}</th>
                            <th>
                                <a href="{{route('admin.products.show', $comment->product->id)}}">
                                    {{$comment->product->name}}
                                </a>
                            </th>
                            <th>
                                {{$comment->text}}
                            </th>
                            <th>
                                <span class="{{$comment->getRawOriginal('approved') ? 'text-success': 'text-danger'}}">
                                    {{$comment->approved}}
                                </span>
                            </th>
                            <th>
                                <a class="btn btn-sm btn-outline-success mb-2"
                                   href="{{route('admin.comments.show', $comment->id)}}">نمایش</a>
                                <form action="{{route('admin.comments.destroy', $comment->id)}}" method="post">
                                    @method('DELETE')
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger">حذف</button>
                                </form>
                            </th>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-5">
                {{$comments->render()}}
            </div>
        </div>
    </div>
@endsection
