export const renderGender = gender => {
    if (gender === 'MALE') return 'Masculino';
    return 'Feminino';
};

export const renderRole = role => {
    if (role === 'PROFESSIONAL') {
        return 'Profissional de Saúde';
    }

    if (role === 'INTERN') {
        return 'Investigador';
    }

    if (role === 'ADMIN') {
        return 'Administrador';
    }

    if (role === 'PATIENT') {
        return 'Utente';
    }
};

export const getCategoryNameById = (id, categories) => {
    let category = 'Sem categoria profissional';
    categories.forEach(cat => {
        if (cat.id === id) {
            category = cat.name;
        }
    });
    return category;
};

export const renderDiseaseType = type => {
    if (type === 'A') {
        return 'Alergia';
    }

    if (type === 'D') {
        return 'Patologia';
    }
};

export const renderDiseaseStringToType = type => {
    if (type === 'Alergia') {
        return 'A';
    }

    if (type === 'Patologia') {
        return 'D';
    }
};

export const parseDateToString = date => {
    let dd = date.getDate() < 9 ? `0${date.getDate()}`: date.getDate();
    let mm = date.getMonth() + 1 < 9 ? `0${date.getMonth() + 1}`: date.getDate() + 1;
    let yyyy = date.getFullYear();

    return `${dd}/${mm}/${yyyy}`;
};

export const parseMealTypeToString = (type) => {
    switch (type) {
        case 'P': return 'Pequeno-almoço';
        case 'M': return 'Meio da manhã'
        case 'A': return 'Almoço';
        case 'J': return 'Jantar';
        case 'L': return 'Lanche';
        case 'S': return 'Snack';
        default: return 'Ceia';
    }
};

export const parseMonth = (month) => {
    switch (month) {
        case '01': return 'Janeiro';
        case '02': return 'Fevereiro';
        case '03': return 'Março';
        case '04': return 'Abril';
        case '05': return 'Maio';
        case '06': return 'Junho';
        case '07': return 'Julho';
        case '08': return 'Agosto';
        case '09': return 'Setembro';
        case '10': return 'Outubro';
        case '11': return 'Novembro';
        case '12': return 'Dezembro';
    }
}

export const parseSocketMessage = (data) => {
    try {
        const parsedArray = String(data).replaceAll('"', "").split(":");
        const eventType = parsedArray[1].split(",")[0];
        const receiverId = parsedArray[6].split(",")[0];

        return {
            eventType,
            receiverId
        };
    } catch (e) {
        return null;
    }
}

export const makeSocketEvent = (type, message) => {
    return `"{type:'${type}',message:{senderId:${message.senderId},senderName:${message.senderName},senderPhotoUrl:${message.senderPhotoUrl},receiverId:${message.receiverId},receiverName:${message.receiverName},receiverPhotoUrl:${message.receiverPhotoUrl},message:${message.message},read:${message.read},refMessageId:${message.refMessageId}}}"`;
}
