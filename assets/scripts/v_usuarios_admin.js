
/*************** VALIDACION DE FORMULARIO "REGISTRAR NUEVO USUARIO" **********/

// Seleccionamos las variables y los inputs del formulario
const admin_register_form = document.querySelector('#admin_register_form');
const userName = document.querySelector("#user_name");
const userLastName = document.querySelector("#user_lastname");
const userEmail = document.querySelector('#user_email');
const userTel = document.querySelector('#user_tel');
const userDate = document.querySelector('#user_date');
//const userAdress = document.querySelector('#user_adress');
const userLoginName = document.querySelector('#user_login_name');
const userPassword = document.querySelector('#user_password');

// Definimos las funciones que nos permitirán realizar la validación de los inputs
function validateName(user_name){
    let regex = /^[a-zA-Z ]{2,45}$/;

    return regex.test(user_name);
}

function validateLastName(user_lastname){
    let regex = /^[a-zA-Z ]{2,45}$/;

    return regex.test(user_lastname);
}

function validateEmail(user_email){
    let regex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;

    return regex.test(user_email);
}
 
function validateTel(user_tel){
    let regex = /^\d{9}$/;

    return regex.test(user_tel);
}

function validateBirthDate(user_date) {

    // Campo vacío
    if (!user_date) {
        return {
            ok: false,
            error: "La fecha de nacimiento es obligatoria"
        };
    }

    // Convertir string a Date
    const birthDate = new Date(user_date);
    const today = new Date();

    // Fecha inválida
    if (isNaN(birthDate.getTime())) {
        return {
            ok: false,
            error: "La fecha introducida no es válida"
        };
    }

    // Fecha futura
    if (birthDate > today) {
        return {
            ok: false,
            error: "La fecha no puede ser futura"
        };
    }

    // Calcular edad real
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();

    if (
        monthDiff < 0 || 
        (monthDiff === 0 && today.getDate() < birthDate.getDate())
    ) {
        age--;
    }

    // Mayoría de edad
    if (age < 18) {
        return {
            ok: false,
            error: "Debes ser mayor de edad"
        };
    }

    // Fecha válida
    return {
        ok: true,
        value: user_date
    };
}

function validateLoginName(user_login_name){
    let regex = /^[a-zA-Z0-9]{6,10}$/;

    return regex.test(user_login_name);
}

function validatePassword(user_password){
    let regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[.,_\-])[a-zA-Z\d.,_\-]{4,10}$/

    return regex.test(user_password);
}

// Función ON BLUR de todos los elementos MENOS el de fecha de nacimiento (TIPO DATE -> validación especial)
function validateOnBlur(inputElement, validator){
    if (!inputElement) return;

    inputElement.addEventListener('blur', function(){
        let value = inputElement.value;
        let valid = validator(value);
        let smallElement = inputElement.nextElementSibling;

        if(!valid){
            smallElement.textContent = "Error: El contenido introducido no es válido";
            smallElement.style.color = "red";
            smallElement.style.visibility = "visible";
        }else{
            smallElement.style.visibility = "hidden";
            smallElement.textContent = '';
        }
    });
}

// Función ON BLUR SOLO para el input DATE
function validateDateOnBlur(inputElement){
    if (!inputElement) return;

    inputElement.addEventListener('blur', function(){
        const result = validateBirthDate(inputElement.value);
        const smallElement = inputElement.nextElementSibling;

        if(!result.ok){
            smallElement.textContent = result.error;
            smallElement.style.color = "red";
            smallElement.style.visibility = "visible";
        }else{
            smallElement.textContent = "";
            smallElement.style.visibility = "hidden";
        }
    });
}


if(admin_register_form) {

    admin_register_form.addEventListener('submit', function(e){

    const isNameValid = validateName(userName.value);
    const isLastNameValid = validateLastName(userLastName.value);
    const isEmailValid = validateEmail(userEmail.value);
    const isTelValid = validateTel(userTel.value);
    const dateResult = validateBirthDate(userDate.value);
    const isLoginNameValid = validateLoginName(userLoginName.value);
    const isPasswordValid = validatePassword(userPassword.value);

    if (
        !isNameValid ||
        !isLastNameValid ||
        !isEmailValid ||
        !isTelValid ||
        !dateResult.ok ||
        !isLoginNameValid ||
        !isPasswordValid
    ) {
        alert("Por favor, complete los campos que son obligatorios")
        e.preventDefault();
    };
    
})};

validateOnBlur(userName, validateName);
validateOnBlur(userLastName, validateLastName);
validateOnBlur(userEmail, validateEmail);
validateOnBlur(userTel, validateTel);
validateDateOnBlur(userDate); // SOLO DATE
validateOnBlur(userLoginName, validateLoginName);
validateOnBlur(userPassword, validatePassword);




/*************** VALIDACION DE FORMULARIO "MODIFICAR USUARIO"*************/

const adminUsersContainer = document.getElementById('admin_users_container');

adminUsersContainer.addEventListener('click', e => {

    // ===== MOSTRAR FORMULARIO DE EDICIÓN =====
    const btnModificar = e.target.closest('.btn_modificar_usuario');
    if (btnModificar) {

        const card = btnModificar.closest('.admin_user_card');
        if (!card) return;

        // Cerrar cualquier otro formulario abierto
        document
            .querySelectorAll('.admin_user_edit:not(.hidden)')
            .forEach(el => el.classList.add('hidden'));

        const edit = card.querySelector('.admin_user_edit');
        if (!edit) return;

        edit.classList.remove('hidden');
        return;
    }

    // ===== OCULTAR FORMULARIO DE EDICIÓN =====
    const btnCancel = e.target.closest('.admin_btn_cancel');
    if (btnCancel) {

        const card = btnCancel.closest('.admin_user_card');
        if (!card) return;

        const edit = card.querySelector('.admin_user_edit');
        if (!edit) return;

        edit.classList.add('hidden');
    }
});


