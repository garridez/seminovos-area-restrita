
module.exports.seletor = '.c-meus-dados.a-alterar-senha';

module.exports.callback = ($) => {
    var ShowPassword = require('components/ShowPassword');
    ShowPassword($("input[type='password']"));

    let atualSenha = $("input[name='senhaAtual']");
    let novaSenha = $("input[name='senha']");
    let confNovaSenha = $("input[name='confirmacaoSenha']");
    var Alert = require('components/Alerts');
    if($(atualSenha).hasClass("is-valid")){
        Alert.success('Sua senha foi atualizada com sucesso', 'Sucesso');        
    }
    $("input").change(function() {
        validForm();
    });
    function validForm() {
        if(novaSenha.val().length >= 4 ){
            alterarErros(novaSenha,"Ok essa é uma senha boa","");
            validar(novaSenha);
            if(novaSenha.val() == confNovaSenha.val()){
                alterarErros(confNovaSenha,"Ok essa é uma senha boa","");
                validar(confNovaSenha);
                $("button[type='submit']").prop("disabled", false);
                return;
            }
        }
        if(novaSenha.val() != atualSenha.val()){
            alterarErros(novaSenha,"","");
            validar(atualSenha);
            validar(novaSenha);
        }
        if(novaSenha.val() == atualSenha.val()){
            alterarErros(atualSenha,"","Sua senha nova não pode ser sua antiga senha");
            alterarErros(novaSenha,"","Sua senha nova não pode ser sua antiga senha");
            invalidar(atualSenha);
            invalidar(novaSenha);
        }
        if(novaSenha.val() != confNovaSenha.val()){
            alterarErros(novaSenha,"","As senhas não combinam");
            alterarErros(confNovaSenha,"","As senhas não combinam");
            invalidar(novaSenha);
            invalidar(confNovaSenha);
        }
        if(novaSenha.val().length < 4){
            alterarErros(novaSenha,"","Sua senha deve conter mais de 4 caractéres");
            invalidar(novaSenha);
        }
    }
    function invalidar(elemento){
        $(elemento).removeClass("is-valid");
        $(elemento).addClass("is-invalid");
    }
    function validar(elemento){
        $(elemento).removeClass("is-invalid");
        $(elemento).addClass("is-valid");
    }
    function alterarErros(elemento,textoValid,textoInvalid) {
        let pai = $(elemento).parent();
        $(pai).find(".invalid-feedback").text(textoInvalid);
        $(pai).find(".valid-feedback").text(textoValid);
    }
};
