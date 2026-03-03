const user_profile_form = document.querySelector('#user_profile_form');
const user_password_form = document.querySelector('#user_password_form');
const userName = document.querySelector("#user_name");
const userLastName = document.querySelector("#user_lastname");
const userEmail = document.querySelector('#user_email');
const userTel = document.querySelector('#user_tel');
const userDate = document.querySelector('#user_date');
const userPassword = document.querySelector('#password');
const userPassword2 = document.querySelector('#password2');
//const userAdress = document.querySelector('#user_adress');



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

function validatePassword(user_password){
    let regex = /^[a-zA-Z0-9]{6,10}$/;

    return regex.test(user_password);
}

// Función ON BLUR de todos los elementos MENOS el de fecha de nacimiento (TIPO DATE -> validación especial)

function validateOnBlur(inputElement, validator){
    if (!inputElement) return;

    inputElement.addEventListener('blur', function(){
        let value = inputElement.value;
        let valid = validator(value);
        let smallElement = inputElement.closest('.input_zone')
            .querySelector('.input_error');

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
        const smallElement = inputElement.closest('.input_zone')
            .querySelector('.input_error');

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


if(user_profile_form) {

    user_profile_form.addEventListener('submit', function(e){

        let errores = false;

        // Nombre
        if (!validateName(userName.value)) {
            mostrarError(userName, "Nombre inválido");
            errores = true;
        }

        // Apellidos
        if (!validateLastName(userLastName.value)) {
            mostrarError(userLastName, "Apellidos inválidos");
            errores = true;
        }

        // Email
        if (!validateEmail(userEmail.value)) {
            mostrarError(userEmail, "Email inválido");
            errores = true;
        }

        // Teléfono
        if (!validateTel(userTel.value)) {
            mostrarError(userTel, "Teléfono inválido");
            errores = true;
        }

        // Fecha
        const dateResult = validateBirthDate(userDate.value);
        if (!dateResult.ok) {
            mostrarError(userDate, dateResult.error);
            errores = true;
        }

        if (errores) {
            e.preventDefault();
            alert("Revisa los campos marcados en rojo");
        }
    });
}

function mostrarError(input, mensaje) {
    const zone = input.closest('.input_zone');
    const small = zone.querySelector('.input_error');
    small.textContent = mensaje;
    small.style.color = "red";
    small.style.visibility = "visible";
}


validateOnBlur(userName, validateName);
validateOnBlur(userLastName, validateLastName);
validateOnBlur(userEmail, validateEmail);
validateOnBlur(userTel, validateTel);
validateDateOnBlur(userDate); // SOLO DATE


// Validamos ahora el formulario de PASSWORD
if(user_password_form) {

    user_password_form.addEventListener('submit', function(e){

    const isPasswordValid = validatePassword(userPassword.value);
    const isPassword2Valid = validatePassword(userPassword2.value);
    
    if (
        !isPasswordValid ||
        !isPassword2Valid
    ) {
        alert("Por favor, complete los campos obligatorios");
        e.preventDefault();
    };
    
})};

validateOnBlur(userPassword, validatePassword);
validateOnBlur(userPassword2, validatePassword);
