import { useCreateTransaction } from "../hooks/useCreateTransaction";

const { createTransaction, loading } =
  useCreateTransaction();

const handleSubmit = async () => {
  const e = validate();

  if (Object.keys(e).length) {
    setErrors(e);
    return;
  }

  try {
    const payload = {
      ...form,
      amount: parseFloat(form.amount),

      category:
        form.category === "other"
          ? form.custom_category
          : form.category,
    };

    delete payload.custom_category;

    await createTransaction(payload);

    onSuccess?.();
    onClose?.();
  } catch (error: any) {
    console.error(error);
  }
};