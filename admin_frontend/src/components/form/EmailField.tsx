import InputField from "./InputField"

export const EmailField = (props: Omit<InputFieldProps, 'type' | 'rules'>) => {
  return (
    <InputField
      {...props}
      type="email"
      rules={{
        required: 'Email is required',
        pattern: {
          value: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
          message: 'Invalid email format',
        },
      }}
    />
  )
}