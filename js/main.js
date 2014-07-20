/**
 * Created by denis on 5/25/14.
 */

var secretField = {
    originalData: {},
    edit: function(input){
        if(parseInt(input)==1 || parseInt(input)==2 || parseInt(input)==3){
            $('.display_secret label:nth-child('+input+') input').prop('readonly', false);
        } else if(parseInt(input)==4){
            $('.display_secret textarea').prop('readonly', false);
        }
        $('.display_secret label:nth-child('+input+') button').hide();
        $('.display_secret .glyphicon-floppy-saved').show();
        $('.display_secret .glyphicon-repeat').show();
    },
    reveal: function(){
        $.ajax({
            type: 'POST',
            url: '../includes/get_password.php?x='+nocache(),
            data: {
                u: user,
                s: secrets.active
            },
            beforeSend:function(){
                loading.start();
            },
            success:function(data){
                // successful request
                var displayPassword = $.base64.decode(data);
                $('.display_secret label:nth-child(2) input').attr('type', 'text').val(displayPassword);
                $('#show_password').removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close');
                loading.end();
            },
            error:function(){}
        });
    },
    conceal: function(){
        loading.end();
        $('.display_secret label:nth-child(2) input').attr('type', 'password').val('');
        $('#show_password').removeClass('glyphicon-eye-close').addClass('glyphicon-eye-open');
    },
    revealHandler: function(){
        if($('.display_secret label:nth-child(2) input').attr('type') == 'text'){
            secretField.conceal();
        } else {
            secretField.reveal();
        }
    },
    revert: function(){
        secretField.conceal();
        $("input[name='secret_username']").val(secretField.username).prop('readonly', true);
        $("input[name='secret_password']").val(secretField.password).prop('readonly', true);
        $("input[name='secret_url']").val(secretField.url).prop('readonly', true);
        $("textarea[name='secret_notes']").val(secretField.notes).prop('readonly', true);
        $('.display_secret button').show();
        $('.display_secret .glyphicon-floppy-saved').hide();
        $('.display_secret .glyphicon-repeat').hide();
    },
    setOriginal: function(){
        secretField.username = $("input[name='secret_username']").val();
        secretField.password = $("input[name='secret_password']").val();
        secretField.url = $("input[name='secret_url']").val();
        secretField.notes = $("textarea[name='secret_notes']").val();
    }
};

var secrets = {
    idStart: "secret_",
    active: null,
    hide: function(){
        secrets.active = null;
        secretField.originalData = {};
        $('.display_secret').parents('tr').remove();
    },
    display: function(secret_id){
        if(secrets.active == secret_id){
            secrets.hide();
            loading.end();
        } else {
            $.ajax({
                type: 'POST',
                url: '../includes/display_secret.php?x='+nocache(),
                data: {
                    user: user,
                    secret_id: secret_id
                },
                beforeSend:function(){
                    $('.alert').remove();
                    loading.start();
                    if($(".display_secret") != []){
                        $('.display_secret').parents('tr').remove();
                    }
                },
                success:function(data){
                    // successful request
                    var displayData = $.base64.decode(data);
                    secrets.active = secret_id;
                    $('#'+secrets.idStart+secret_id).after(displayData);
                    secretField.setOriginal();
                    loading.end();
                },
                error:function(){}
            });
        }
    },
    displayHandler: function(){
        loading.start();
        var id = parseInt( $(this).attr('id').replace(secrets.idStart, '') );
        secrets.display(id);
    },
    displayAll: function(){
        loading.start();
        paging.totalElements = -1;
        $.ajax({
            type: 'POST',
            url: '../includes/get_secret_count.php?x='+nocache(),
            data: {
                user: user
            },
            beforeSend:function(){},
            success: function(data){
                paging.totalElements = parseInt(data);
            },
            error:function(){}
        });

        $.ajax({
            type: 'POST',
            url: '../includes/get_secret_names.php?x='+nocache(),
            data: {
                limit : paging.current,
                user: user
            },
            beforeSend:function(){},
            success:function(data){
                var displayData = $.base64.decode(data);
                $('table').append(displayData);
                $('.secret_row').bind('click', secrets.displayHandler);
                loading.end();
            },
            error:function(){}
        });
    },
    clearNew: function(){
        // clear all data from add secret modal
        $("#add-name").val('');
        $("#add-username").val('');
        $("#add-password").val('');
        $("#add-url").val('');
        $("#add-notes").val('');
    },
    save: function(secret_id){
        loading.start();
        // TODO - validate everything is OK.
        var secretData = {};
        if(typeof secret_id == 'undefined'){
            secretData.name = $("#add-name").val();
            secretData.username = $("#add-username").val();
            secretData.password = $("#add-password").val();
            secretData.url = $("#add-url").val();
            secretData.notes = $("#add-notes").val();
        } else {
            // get existing secret details
            secretData.id = secret_id;
            $('.display_secret input').each(function(index, obj){
                if(!$(obj).prop('readonly')){
                    secretData[ $(obj).attr('name').replace(secrets.idStart, '') ] = $(obj).val()
                }
            });
            var text = $('.display_secret textarea');
            if(!text.prop('readonly')){
                secretData[ text.attr('name').replace(secrets.idStart, '') ] = text.val();
            }
        }
        secretData.user = user;

        $.ajax({
            type: 'POST',
            url: '../includes/save_secret.php?x='+nocache(),
            data: {
                data: prepareDataTransfer(JSON.stringify(secretData), true)
            },
            beforeSend:function(){},
            success: function(data){
                secrets.refreshAll();
                if(parseInt(data)){
                    displayAlert('success', 'Secret Saved');
                }
            },
            error:function(){}
        });
    },
    del: function(secret_id){
        if(confirm("Are you user sure you want to delete this secret?")){
            $.ajax({
                type: 'POST',
                url: '../includes/delete_secret.php?x='+nocache(),
                data: {
                    user: user,
                    secret_id: secret_id
                },
                beforeSend:function(){
                    loading.start();
                    $('.display_secret').parents('tr').remove();
                },
                success:function(data){
                    // successful request
                    $('#'+secrets.idStart+secret_id).remove();
                    loading.end();
                    secrets.active = null;
                    if(parseInt(data)){
                        displayAlert('success', 'Secret Deleted');
                    }
                },
                error:function(){}
            });
        }
    },
    refreshAll: function(){
        loading.start();
        secrets.hide();
        $('.secret_row').remove();
        secrets.displayAll();
    }
};

function prepareDataTransfer(data, send){
    if(send){
        return $.base64.encode(data);
    } else {
        return $.base64.decode(data);
    }
}

function displayAlert(alertType, alertText){
    var validAlertTypes = ['info', 'warning', 'success', 'danger'];
    var alertToDisplay = '';
    if($.inArray(alertType, validAlertTypes) > -1){
        alertToDisplay += '<div class="alert alert-'+alertType+' alert-dismissable" role="alert">';
        alertToDisplay += '     <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
        alertToDisplay += '     '+alertText;
        alertToDisplay += '</div>';
    }
    $('.row').before(alertToDisplay);
}

$(function(){
    loading.start();
//    loading.img = 'img/loading.gif';      // TODO - create image before enabling
    paging.nextObj = $('#next');
    paging.prevObj = $('#prev');
    secrets.displayAll();
    paging.reset();

    $('#add-save').click(function(){
        secrets.save();
    });
    $('#add-cancel').click(secrets.clearNew);
    paging.nextObj.val( paging.current+1 ).click(function(){
        paging.next();
        secrets.refreshAll();
    });
    paging.prevObj.hide().click(function(){
        paging.prev();
        secrets.refreshAll();
    });
    displayAlert('info', 'Welcome');
});