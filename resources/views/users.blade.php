<!DOCTYPE html>
<html>
<head>
    <title>SIM AHA Right Brain</title>

    <!-- jquery -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- bootstrap 5 css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Datatables css -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

</head>

<body>

<div class="container">
    <div class="card mt-5">
        <h3 class="card-header p-3">Daftar User</h3>
        <div class="card-body">
            <table id="datatable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sl.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
       
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>       

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {

       const table = $('#datatable').DataTable({
            serverSide: true,
            processing: true,
            ajax: '{{ route("users.index") }}',
           
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'created_at', name: 'created_at'},
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        // Delete user functionally
        $('table').on('click', '.delete-user', function() {
            const userId = $(this).data('id');
            
            if (userId) {
                if (confirm('Are you sure, you want to delete?')) {
                    $.ajax({
                        url: `{{ url('users/delete') }}/${userId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success:function(response) {
                            if (response.status === 'success') {
                                table.ajax.reload(null, false);
                            } else {
                                alert(response.message);
                            }
                        },
                        errror: function(error) {
                            alert('something went wrong!')
                        }
                })

                }
                
            }
        });

        const editableColumns = [1, 2];
        let currentEditableRow = null;

        // Edit user functionally
        $('table').on('click', '.edit-user', function() {
            const userId = $(this).data('id');
            const currentRow = $(this).closest('tr');

            // Check if current row is editable then this will reset the other row edit
            if (currentEditableRow && currentEditableRow !== currentRow) {
                 resetEditableRow(currentEditableRow);
            }

            // Calling Make Editable Row Function
            makeEditableRow(currentRow);

            // Updating the current row to editable
            currentEditableRow = currentRow;

            // Appending action buttons in the lasr column
            currentRow.find('td:last').html(`
                <button class="btn btn-primary btn-sm btn-update" data-id="${userId}">Update</button>
                <button data-id="${userId}" class="btn btn-danger btn-sm delete-user">Delete</button> 
            `);
            
        });

        // Function : Make Editable Row
        function makeEditableRow(currentRow) {
            currentRow.find('td').each(function(index) {

                const currentCell = $(this);
                const currentText = currentCell.text().trim();

               if (editableColumns.includes(index)) {
                    currentCell.html(`<input type="text" class="form-control editable-input" value="${currentText}" />`);
               }
            });

            
        }

        // Function : Reset Current Row Editable
        function resetEditableRow(currentEditableRow) {
            currentEditableRow.find('td').each(function(index) {

                const currentCell = $(this);

                if (editableColumns.includes(index)) {
                    const currentValue = currentCell.find('input').val();
                    currentCell.html(`${currentValue}`);
                }
            });

            const userId = currentEditableRow.find('.btn-update').data('id');

            currentEditableRow.find('td:last').html(`
                <button class="btn btn-success btn-sm btn-edit" data-id="${userId}">Edit</button>
                <button data-id="${userId}" class="btn btn-danger btn-sm delete-user">Delete</button>
            
            `);
        }

        // Update function
        $('table').on('click', '.btn-update', function() {
            const userId = $(this).data('id');

            const currentRow = $(this).closest('tr');

            const updatedUserData = {};

            currentRow.find('td').each(function(index) {
                if (editableColumns.includes(index)) {
                    const inputValue = $(this).find('input').val();

                    if(index === 1)
                        updatedUserData.name = inputValue;

                    if(index === 2)
                        updatedUserData.email = inputValue;

                }
            });


            // Ajax call to update user data

            $.ajax({
                url: '{{ route("users.update") }}',
                type: 'PUT',
                data: {
                    id: userId,
                    name: updatedUserData.name,
                    email : updatedUserData.email,
                    _token: '{{ csrf_token() }}',
                },
                success: function(response){
                    if (response.status === 'success') {
                        table.ajax.reload(function() {
                             $('#row-id-' + userId).addClass('highlight');
                        });
                    } else {
                        alert(response.message);
                    }
                },
                error: function(errorResponse) {
                    alert(errorResponse.message);
                }
            });
        });
    });

 
</script>
</body>
</html>