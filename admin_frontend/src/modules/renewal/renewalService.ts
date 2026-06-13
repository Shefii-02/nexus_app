renewalDue() {
  return apiClient.get(
    '/renewals/due'
  )
},

renewalHistory() {
  return apiClient.get(
    '/renewals'
  )
},

payRenewal(payload: any) {
  return apiClient.post(
    '/renewals/pay',
    payload
  )
},