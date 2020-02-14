<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>laravel 6  Ajax CRUD Application</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
 
 <style>
   .container{
    padding: 0.5%;
   } 
</style>
</head>
<body>
 
<div class="container">
    <div class="row">
        <div class="col-12">
          <a href="javascript:void(0)" class="btn btn-success mb-2" id="create-new-product">Add Product</a> 
          <table class="table table-bordered" id="laravel_crud">
           <thead>
              <tr>
                 <th>Id</th>
                 <th>Product Title</th>
                 <th>Product Description</th>
                 <td colspan="2">Action</td>
              </tr>
           </thead>
           <tbody id="products-crud">
              @foreach($products as $p_info)
              <tr id="product_id_{{ $p_info->id }}">
                 <td>{{ $p_info->id  }}</td>
                 <td>{{ $p_info->title }}</td>
                 <td>{{ $p_info->description }}</td>
                 <td colspan="2">
                    <a href="javascript:void(0)" id="edit-product" data-id="{{ $p_info->id }}" class="btn btn-info mr-2">Edit</a>
                    <a href="javascript:void(0)" id="delete-product" data-id="{{ $p_info->id }}" class="btn btn-danger delete-product">Delete</a>
                  </td>
              </tr>
              @endforeach
           </tbody>
          </table>
          {{ $products->links() }}
       </div> 
    </div>
</div>

<!--Modal-->
<div class="modal fade" id="ajax-crud-modal" aria-hidden="true" >
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="productCrudModal"></h4>
        </div>
        <form id="productForm" name="productForm" class="form-horizontal"  >
        @csrf
          <div class="modal-body">
              <input type="hidden" name="product_id" id="product_id">
              <div class="form-group">
        <label for="title">Title</label>
        <input type="text" name="title" class="form-control" id="title" placeholder="Product Title">
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <input type="text" name="description" class="form-control" id="description" placeholder="Product Description">
      </div>      
          </div>
          <div class="modal-footer">
              <button type="submit" class="btn btn-primary" id="btn-save" value="create">
                Save changes
              </button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </form>
    </div>
  </div>
</div>

 
</body>

<!--Ajax CRUD Logic-->
<script >
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    /*  When user click add  button */
    $('#create-new-product').click(function () {
        $('#btn-save').val("create-product");
        $('#productForm').trigger("reset");
        $('#productCrudModal').html("Add New Product");
        $('#ajax-crud-modal').modal('show');
    });
 
   /* When click edit  */
    $('body').on('click', '#edit-product', function () {
      var product_id = $(this).data('id');
      $.get('products/' + product_id +'/edit', function (data) {
         $('#productCrudModal').html("Edit Product");
          $('#btn-save').val("edit-product");
          $('#ajax-crud-modal').modal('show');
          $('#product_id').val(data.id);
          $('#title').val(data.title);
          $('#description').val(data.description);
      })
     
   });
   //When click delete 
    $('body').on('click', '.delete-product', function () {
        var product_id = $(this).data("id");
        if(confirm("Are You sure want to delete !")) {
 
        $.ajax({
            type: "DELETE",
            url: "{{ url('products')}}"+'/'+product_id,
            success: function (data) {
                $("#product_id_" + product_id).remove();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
       }
    });   
  });

////// Logic for Add and Update

if ($("#productForm").length > 0) {
      $("#productForm").validate({
 
     submitHandler: function(form) {
 
      var actionType = $('#btn-save').val();
      $('#btn-save').html('Sending..');
      
      $.ajax({
          data: $('#productForm').serialize(),
          url: "{{ route('products.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
    
              var product = '<tr id="product_id_' + data.id + '"><td>' + data.id + '</td><td>' + data.title + '</td><td>' + data.description + '</td>';
              product += '<td colspan="2"><a href="javascript:void(0)" id="edit-product" data-id="' + data.id + '" class="btn btn-info mr-2">Edit</a>';
                product += '<a href="javascript:void(0)" id="delete-product" data-id="' + data.id + '" class="btn btn-danger delete-product ml-1">Delete</a></td></tr>';
               
              
              if (actionType == "create-product") {
                  $('#products-crud').prepend(product);
              } else {
                  $("#product_id_" + data.id).replaceWith(product);
              }
 
              $('#productForm').trigger("reset");
              $('#ajax-crud-modal').modal('hide');
              $('#btn-save').html('Save Changes');
              
              
          },
          error: function (data) {

              console.log('Error:', data);
              $('#btn-save').html('Save Changes');

          }
      });
    }
  })
}



</script>
</html>