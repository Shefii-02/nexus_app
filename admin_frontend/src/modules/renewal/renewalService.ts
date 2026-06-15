import apiClient from '../../services/apiClient'

export const renewalService = {
   getAll: (params?: any) =>
    apiClient.get('/renewals', {
      params,
    }),

  renewalDue() {
    return apiClient.get('/renewals/due')
  },

  renewalHistory() {
    return apiClient.get('/renewals')
  },

  payRenewal(payload: any) {
    return apiClient.post('/renewals/pay', payload)
  },

}