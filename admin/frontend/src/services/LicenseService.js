import ApiService from './ApiService'
export const licenseValidationCheck = async (data) => {
    try {
        const response = await ApiService.fetchData({
            url: `/premium/license-validation`,
            method: 'post',
            data
        })
        return response.data;

    } catch (error) {
        return error.message;
    }
}

export const licenseDelete = async (data) => {
    try {
        const response = await ApiService.fetchData({
            url: `/premium/license-delete`,
            method: 'post',
            data
        })
        return response.data;

    } catch (error) {
        return error.message;
    }
}

export const getLicenseKey = async () => {
    try {
        const response = await ApiService.fetchData({
            url: `/premium/get-license-key`,
            method: 'get'
        })
        return response.data;

    } catch (error) {
        return error.response.data;
    }
}



