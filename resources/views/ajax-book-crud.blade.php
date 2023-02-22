<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Ajax Book CRUD</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" >

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style type="text/css">
        .form-group > span{
            color: red;
        }
    </style>
</head>
<body>

<div class="container mt-2">
    <div class="row">
        <div class="col-md-12 card-header text-center">
          <h2>Laravel Ajax Book CRUD</h2>
        </div>
        <div class="col-md-12 mt-1 mb-2"><button id="addNewBook" class="btn btn-success">Add</button></div>
        <div class="col-md-12">
            <table class="table" id="my_table">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Book Title</th>
                  <th scope="col">Book Code</th>
                  <th scope="col">Book Author</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody class="book_body"> 
                @foreach ($books as $book)
                <tr id="book_id_{{$book->id}}">
                    <td>{{ $book->id }}</td>
                    <td class="td_title_{{$book->id}}">{{ $book->title }}</td>
                    <td class="td_code_{{$book->id}}">{{ $book->code }}</td>
                    <td class="td_author_{{$book->id}}">{{ $book->author }}</td>
                    <td>
                       <a href="javascript:void(0)" class="btn btn-primary edit" data-id="{{ $book->id }}">Edit</a>
                      <a href="javascript:void(0)" class="btn btn-danger delete" data-id="{{ $book->id }}">Delete</a>
                    </td>
                </tr>
                @endforeach

                @if(isset($empty))
                    <h3>Data Not Found ! </h3>
                @endif

              </tbody>
            </table>
            {{$books->links("pagination::bootstrap-4")}}
            <!-- {!! $books->links() !!} -->
        </div>
    </div>        
</div>

<!-- boostrap model -->
<div class="modal fade" id="ajax-book-model" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="ajaxBookModel"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="addEditBookForm" class="form-horizontal" method="POST">
                    <input type="hidden" name="id" id="id">
                    <div class="d-none"></div>
                    <div class="form-group">
                        <label for="name" class="control-label">Book Name</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter Book Name" required/>
                        <span id="error"></span>
                    </div>

                    <div class="form-group">
                        <label for="name" class="control-label">Book Code</label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="Enter Book Code" value=""required/>
                        <span id="error"></span>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Book Author</label>
                        <input type="text" class="form-control" id="author" name="author" placeholder="Enter author Name" value="" required="" />
                        <span id="error"></span>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="btn-save" value="addNewBook">Save changes</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.js"></script>

    
<script type="text/javascript">
    $(document).ready(function(){
        $('#my_table').dataTable(); 

        var form_type ;
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $("#addNewBook").click(function(){
            $("#addEditBookForm").trigger("reset");
            $("#ajaxBookModel").html("Add Book");
            $("#ajax-book-model").modal("show");
            $('.d-none').text('add');
            form_type = 'add';

            $("#error").text(' ');
            $('input[name="title"]').parents('.form-group').find('#error').text('');
            $('input[name="code"]').parents('.form-group').find('#error').text('');
            $('input[name="author"]').parents('.form-group').find('#error').text('');
        });

        $(document).on("click", ".edit", function () {
            var id = $(this).data("id");

            form_type = 'edit';
            $("#error").text(' ');
            $('input[name="title"]').parents('.form-group').find('#error').text('');
            $('input[name="code"]').parents('.form-group').find('#error').text('');
            $('input[name="author"]').parents('.form-group').find('#error').text('');

            $.ajax({
                type: "POST",
                url: "{{ url('edit-book') }}",
                data: { id: id },
                dataType: "json",
                success: function (res) {

                    $("#ajaxBookModel").html("Edit Book");
                    $("#ajax-book-model").modal("show");
                    $("#id").val(res.id);
                    $("#title").val(res.title);
                    $("#code").val(res.code);
                    $("#author").val(res.author);
                },
            });
        });

        $("body").on("click", "#btn-save", function() {
            var id = $("#id").val();
            var title = $("#title").val();
            var code = $("#code").val();
            var author = $("#author").val();

            $("#btn-save").html("Please Wait...");
            $("#btn-save").attr("disabled", true);

            $.ajax({
                type: "POST",
                url: "{{ url('add-update-book') }}",
                data: {id: id, title: title, code: code,author: author},
                dataType: "json",
                success: function (response) {
                    if(response.status == 'error') {
                        $.each(response.errors, function (key, value) {
                            var input = '#addEditBookForm input[name=' + key + ']';
                            $('input[name="'+key+'"]').parents('.form-group').find('#error').text(value);
                        });
                    }else{
                        var res    =   response.book;
                        if(form_type == 'add'){
                            var elem='';
                            elem += '<tr book_id_'+res.id+'>'+
                                        '<td>'+res.id+'</td>'+
                                        '<td>'+res.title+'</td>'+
                                        '<td>'+res.code+'</td>'+
                                        '<td>'+res.author+'</td>'+
                                        '<td><a href="javascript:void(0)" class="btn btn-primary edit" data-id="'+res.id+'">Edit</a>'+
                                        '<a href="javascript:void(0)" class="btn btn-danger delete" data-id="'+res.id+'">Delete</a>'+
                                        '</td>'+
                                    '</tr>';
                            $('.book_body').prepend(elem); 
                        }else{
                            //$('#my_table').DataTable().ajax.reload();

                            $(".td_title_"+id).text(res.title);
                            $(".td_code_"+id).text(res.code);
                            $(".td_author_"+id).text(res.author);
                        }
                        
                        $('#ajax-book-model').modal('hide');
                    }
                    $("#btn-save").html("Submit");
                    $("#btn-save").attr("disabled", false);
                    console.log(form_type);
                },
            });
        });

        $("body").on("click", ".delete", function () {
            if (confirm("Delete Record?") == true) {
                var id = $(this).data("id");

                $.ajax({
                    type: "POST",
                    url: "{{ url('delete-book') }}",
                    data: { id: id },
                    dataType: "json",
                    success: function (res) {
                        //window.location.reload();
                        $('#book_id_'+id).remove();
                    },
                });
            }
        });
    });
    $(document).on('keyup','input',function(){
        var value  =  $(this).val();
        var key   =  this.getAttribute('name');
        if(value){
            $('input[name="'+key+'"]').parents('.form-group').find('#error').text('');
        }else{
            $('input[name="'+key+'"]').parents('.form-group').find('#error').text('The '+key+' field is required.');
        }
    })
</script>

</body>
</html>