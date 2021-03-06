<section class="section">
    <div class="section-header">
        <h1>No Kamar</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Master Data</a></div>
            <div class="breadcrumb-item">No Kamar</div>
        </div>
    </div>

    <div class="section-body">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus"></i> Tambah Data</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped nowrap" id="table-3" style="width:100%;">
                            <thead>                                 
                                <tr>
                                    <th>No</th>
                                    <th>No Kamar</th>
                                    <th>Tipe Kamar</th>
                                    <th>Kuota</th>
                                    <th style="padding-right:80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<div class="modal fade" tabindex="-1" role="dialog" id="exampleModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" id="form-1" class="form"  onsubmit="return false">
                <input type="hidden" name="id"/>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>No Kamar</label>
                    <input class="form-control" name="room_number" type="number" min="1" placeholder="No Kamar"> <br>
                    <label>Tipe Kamar</label>
                    <select class="form-control" name="room_type">
                        <option value="0">-- Pilih Tipe Kamar --</option>
                        <?php foreach ($room_types as $key => $value) { ?>
                            <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                        <?php }?>
                    </select> <br>
                    <label>Kuota</label>
                    <input class="form-control" name="qty" type="number" min="1" placeholder="Kuota">

                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="btn-submit" type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Tambah Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    /* Button Edit Action */
    $('#exampleModal').on('hidden.bs.modal', function () {
        $('#form-1 #btn-submit').html('<i class="fa fa-save"></i> Tambah Data');
        $('#form-1')[0].reset();
        $('#form-1 [name="id"]').val('');
    });

    $(document).ready(function () {

        dataTableVar = $('#table-3').DataTable({
            'ordering': false,
            'info': true,
            "bFilter": true,
            "serverSide": true,
            "processing": true,
            "ajax": {
                "url": "<?php echo base_url('rooms/get_datatable'); ?>",
                "type": "POST"
            },
            "columns": [
                {  
                    "orderable": false
                },
                {
                    "orderable": false
                },
                {
                    "orderable": false
                },
                {
                    "orderable": false
                },
                {
                    "orderable": false,
                    'className': 'text-right'
                }
            ]
        });

        $('#form-1').submit(function () {
            /* var modal dan form */
            var modal = '#exampleModal';
            var form = '#form-1';
            if (!$(form).isValid()) {
                $(form + ' #btn-submit').attr('disabled', false); /* set button enable */
                return;
            }
            $(form + ' #btn-submit').text('Menyimpan...');
            $(form + ' #btn-submit').attr('disabled', true);

            $.ajax({
                url: "<?php echo base_url('rooms/CreateOrUpdate') ?>",
                type: "POST",
                data: $(this).serialize(),
                timeout: 5000,
                dataType: "JSON",
                success: function (data)
                {
                    if (data.status) /* if success close modal and reload ajax table */
                    {
                        alert(data.message);
                        $(modal).modal('hide');
                        $(form)[0].reset();
                        dataTableVar.ajax.reload(null, false);
                    } else {
                        alert(data.message);
                    }
                    $(modal).modal('hide');
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Terjadi kesalahan saat menghubungkan ke server.');
                    $(modal).modal('hide');
                }
            });
            $(form + ' #btn-submit').attr('disabled', false); /* set button enable */
        });

        /* Button Edit Action */
        $('#table-3').on('click', '.edit', function () {
            /* var modal dan form */
            var modal = '#exampleModal';
            var form = '#form-1';
            var id = $(this).attr('data-id');

            $(form + ' #btn-submit').html('<i class="fa fa-save"></i> Update Data');

            $.ajax({
                url: "<?php echo base_url('rooms/show_detail') ?>",
                type: "POST",
                data: "data_id=" + id + '&<?php echo $CSRF_CONFIG['name']; ?>=<?php echo $CSRF_CONFIG['hash']; ?>',
                timeout: 5000,
                dataType: "JSON",
                success: function (data)
                {
                    if (data.status) /* if success close modal and reload ajax table */
                    {
                        $(form + ' [name="id"]').val(data.data.id);
                        $(form + ' [name="room_number"]').val(data.data.room_number);
                        $(form + ' [name="room_type"]').val(data.data.room_type_id);
                        $(form + ' [name="qty"]').val(data.data.qty);
                        $(modal).modal('show');
                    } else {
                        alert('Terjadi kesalahan saat menghubungkan ke server.<br/>' + data.pesan);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Terjadi kesalahan saat menghubungkan ke server.');
                }
            });
        });

        /* Button Delete Action */
        $('#table-3').on('click', '.delete', function () {
            /* var modal dan form */
            var id = $(this).attr('data-id');
            bootbox.confirm('Apakah Anda yakin akan menghapus data ini?', function (konfirm) {
                if (konfirm) {
                    $.ajax({
                        type: 'post',
                        url: '<?php echo base_url('rooms/delete'); ?>',
                        data: 'id=' + id + '&<?php echo $CSRF_CONFIG['name']; ?>=<?php echo $CSRF_CONFIG['hash']; ?>',
                        dataType: 'JSON',
                        timeout: 5000,
                        success: function (data) {
                            if (data.status) {
                                alert('Data berhasil dihapus.');
                                dataTableVar.ajax.reload(null, false);
                            } else {
                                alert('Data gagal dihapus.<br>' + data.message);
                            }
                        },
                        error: function () {
                            dataTableVar.ajax.reload(null, false);
                            alert('Terjadi kesalahan saat menghubungkan ke server.');
                        }
                    });
                }
            });
        });

    });
</script>