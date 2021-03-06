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
            url: secrets.requestUri+'?x='+nocache(),
            data: {
                user: user,
                secret_id: secrets.active,
                type: 'password'
            },
            beforeSend:function(){
                loading.start();
            },
            success:function(displayPassword){
                // successful request
                $('.display_secret label:nth-child(2) input').attr('type', 'text').val(displayPassword);
                $('#show_password').removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close');
                loading.end();
            },
            error:function(){
                notice.display('danger', 'WARNING: Issue occurred while trying to show password Secret');
                loading.end();
            }
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
    requestUri: '../includes/request_data.php',
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
                url: secrets.requestUri+'?x='+nocache(),
                data: {
                    user: user,
                    secret_id: secret_id,
                    type: 'get'
                },
                beforeSend:function(){
                    notice.remove();
                    loading.start();
                    if($(".display_secret") != []){
                        $('.display_secret').parents('tr').remove();
                    }
                },
                success:function(displayData){
                    // successful request
                    secrets.active = secret_id;
                    $('#'+secrets.idStart+secret_id).after(displayData);
                    secretField.setOriginal();
                    loading.end();
                },
                error:function(){
                    notice.display('danger', 'WARNING: Issue occurred while trying to display Secret');
                    loading.end();
                }
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
            url: secrets.requestUri+'?x='+nocache(),
            data: {
                user: user,
                type: 'count'
            },
            beforeSend:function(){},
            success: function(data){
                paging.totalElements = parseInt(data);
            },
            error:function(){
                notice.display('danger', 'WARNING: Issue occurred while trying to retrieve Secrets');
                loading.end();
            }
        });

        $.ajax({
            type: 'POST',
            url: secrets.requestUri+'?x='+nocache(),
            data: {
                start : paging.current,
                limit: paging.limit,
                user: user,
                type: 'list'
            },
            beforeSend:function(){},
            success:function(displayData){
                $('table').append(displayData);
                $('.secret_row').bind('click', secrets.displayHandler);
                loading.end();
            },
            error:function(){
                notice.display('danger', 'WARNING: Issue occurred while trying to retrieve Secrets');
                loading.end();
            }
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
        var saveMsg = '';
        if(typeof secret_id == 'undefined'){
            secretData.name = $("#add-name").val();
            secretData.username = $("#add-username").val();
            secretData.password = $("#add-password").val();
            secretData.url = $("#add-url").val();
            secretData.notes = $("#add-notes").val();
            saveMsg = "New Secret Saved";
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
            saveMsg = "Secret Updated";
        }

        $.ajax({
            type: 'POST',
            url: secrets.requestUri+'?x='+nocache(),
            data: {
                user: user,
                data: JSON.stringify(secretData),
                type: 'save'
            },
            beforeSend:function(){
                notice.remove();
            },
            success: function(data){
                secrets.refreshAll();
                if(parseInt(data)){
                    notice.display('success', saveMsg);
                } else {
                    notice.display('danger', 'WARNING: Issue occurred while trying to save Secret');
                }
                loading.end();
            },
            error:function(){
                notice.display('danger', 'WARNING: Issue occurred while trying to save Secret');
                loading.end();
            }
        });
    },
    del: function(secret_id){
        if(confirm("Are you user sure you want to delete this secret?")){
            $.ajax({
                type: 'POST',
                url: secrets.requestUri+'?x='+nocache(),
                data: {
                    user: user,
                    secret_id: secret_id,
                    type: 'delete'
                },
                beforeSend:function(){
                    loading.start();
                    notice.remove();
                    secrets.hide();
                },
                success:function(data){
                    // successful request
                    $('#'+secrets.idStart+secret_id).remove();
                    secrets.active = null;
                    if(parseInt(data)){
                        notice.display('success', 'Secret Deleted');
                    } else {
                        notice.display('danger', 'WARNING: Issue occurred while trying to delete Secret');
                    }
                    loading.end();
                },
                error:function(){
                    notice.display('danger', 'WARNING: Issue occurred while trying to delete Secret');
                    loading.end();
                }
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

var notice = {
    display: function(alertType, alertText){
        var validAlertTypes = ['info', 'warning', 'success', 'danger'];
        var alertToDisplay = '';
        if($.inArray(alertType, validAlertTypes) > -1){
            alertToDisplay += '<div class="alert alert-'+alertType+' alert-dismissable" role="alert">';
            alertToDisplay += '     <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
            alertToDisplay += '     '+alertText;
            alertToDisplay += '</div>';
        }
        $('.row').before(alertToDisplay);
    },
    remove: function(){
        $('.alert').remove();
    }
};

$(function(){
    loading.start();
    loading.img = 'imgs/loading.gif';
    paging.nextObj = $('#next');
    paging.prevObj = $('#prev');
    secrets.displayAll();
    paging.reset();

    $('#add-save').click(function(){
        secrets.save();
        secrets.clearNew();
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
    notice.display('info', 'Welcome');
});