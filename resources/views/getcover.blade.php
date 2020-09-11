<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a href="http://cover.todo.com/admin/cover" class="btn btn-sm btn-danger">Back</a> Get Cover
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form class="form" method="POST" action="{{ backpack_url('post-cover') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="">Nhập địa chỉ folder: </label>
                                    <input type="text" name="ulr" id="" class="form-control" placeholder="">
{{--                                    <input id="upload" name="content" type="file" value="Input" directory webkitdirectory/>--}}
                                </div>
                                <div class="d-flex">
                                    <select class="form-control" name="type" id="type">
                                        <option value="again">Quét lại từ đầu</option>
                                        <option value="continue">Quét tiếp</option>

                                    </select>
                                    <button type="submit" class="btn btn-primary ml-3 w-50">Quét</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-12">
                            <div>
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Đường dẫn</th>
                                        <th>Tên ảnh</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($items))
                                        @foreach($items as $key => $value)
                                            <tr>
                                                <td >{{ $key+1 }}</td>
                                                <td>{{ $value['file'] }}</td>
                                                <td><img width="60px"  src="{{ asset($value['avatar']) }}" alt=""></td>
                                                <td><a data-id="{{ $key }}"  data-file="{{ $value['file'] }}" data-avatar="{{ asset($value['avatar']) }}" id="push-post-new"  class="push-post btn btn-primary btn-sm" style="color:#fff">Đăng wp</a></td>
                                            </tr>

                                        @endforeach
                                    @endif

                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center">
                                @if(!empty($items))
                                {!! $items->links() !!}
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"  ></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" ></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.push-post').click(function() {
            var back = $(this);
            $.ajax({
                method: "POST",
                url: "{{ config('api_wp.word_press.wpURL') }}",
                data: {
                    title: $(this).attr("data-file"),
                    content: '<img src="' + $(this).attr("data-avatar") + '" alt="">',
                    type: 'post',
                    status: 'draft',
                },
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('Authorization', 'Basic ' + "{{ base64_encode(config('api_wp.word_press.wpUser')) }}");
                },
                success: function(response) {
                    back.css("background","red");
                    alert('Đã đăng bài thành công!');

                },
                error: function(request, status, error) {
                    alert(error);
                }
            });
        });
    });
</script>
</body>

</html>
