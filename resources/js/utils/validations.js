const emailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

export const isEmptyField = field => {
    if (!field) return true;

    return String(field).trim() === '';
};

export const isDecimalOrEmpty = field => {
    if (isEmptyField(field)) return true

    try {
        parseFloat(field)
    } catch (e) { return true}

    return false
}

export const isStringLowerThanMin = (field, min) => {
    return field.length < min;
};

export const isStringBiggerThanMax = (field, max) => {
    return field.length > max;
};

export const isEmailFormatInvalid = email => {
    return !emailRegex.test(email);
};

export const equalFields =  (fieldOne, fieldTwo) => {
    return fieldOne ===  fieldTwo;
};

export const isPositiveNumber = field => {
    return field <= 0;
};

export const ERROR_MESSAGES = {
    mandatoryField: 'Campo obrigatório!',
    invalidFormat: 'Formato inválido!',
    invalidNegative: 'O valor não pode ser negativo',
    invalidCredentials: 'Credenciais inválidas!',
    unknownError: 'Woops! Algo correu mal!',
    matchField: 'Os campos devem ser iguais!',
    notMatchFields: 'Os campos não podem ser iguais',
    alreadyExistingProfessionalCategory: 'A categoria profissional já existe!',
    alreadyExistingDisease: 'A doença já existe!',
    maxCharacters: 'Ultrapassa o limite máximo de ',
    alreadyExistingUSF: 'A instituição já está registada',
    minCharacters: 'Deve ter pelo menos ',
    medicationAlreadyExist: 'Verifique se o nome do medicamento já se encontra na lista',
    positiveNumber: 'O valor deve ser superior a 0',
    decimalNumber: 'Valor numérico obrigatório'
};
