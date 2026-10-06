import { useState, useEffect } from "react";
import TextInput from "./TextInput";
import FieldWrapper from "./FieldWrapper";
import { __ } from "@wordpress/i18n";
import { isValidEmail } from "@/utils/validators";

const Email = ({ field, onChange, value, fieldStatus = () => {} }) => {
    const [isValid, setIsValid] = useState(false);
    const [touched, setTouched] = useState(false);

    // Validate and report status on value change.
    // fieldStatus is intentionally omitted from deps: it originates from Accordion
    // and uses functional updaters (setFailed) + synchronous Zustand reads,
    // so a stale closure is safe. Adding it would cause re-fires on every Accordion render.
    useEffect(() => {
        if (!value) {
            setIsValid(false);
            setTouched(false);
            // Only report failure if user has interacted with the field
            // This prevents revoking backend-set _completed on initial render
            if (touched) {
                fieldStatus(false);
            }
            return;
        }

        const valid = isValidEmail(value);
        setIsValid(valid);
        fieldStatus(valid);

        if (!touched) {
            setTouched(true);
        }
    }, [value]);

    const handleBlur = () => {
        setTouched(true);
    };

    return (
        <FieldWrapper inputId={field.id} label={field.label}>
            <div className="relative w-full">
                <TextInput
                    id={field.id}
                    placeholder={field.placeholder && field.placeholder.trim() ? field.placeholder : __("Enter your e-mail address", "ONBOARDING_WIZARD_TEXT_DOMAIN")}
                    type="email"
                    field={field}
                    onChange={(e) => onChange(e.target.value)}
                    onBlur={handleBlur}
                    value={value}
                    className={`w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-0 
                        ${touched && isValid === true ? "border-[#046C4E]" : ""}
                        ${touched && isValid === false ? "border-red" : ""}`}
                />

                {touched && isValid === true && value && (
                    <span className="absolute right-3 top-1/2 -translate-y-1/2 text-[#046C4E]">✔</span>
                )}
                {touched && isValid === false && value && (
                    <span className="absolute right-3 top-1/2 -translate-y-1/2 text-red">✖</span>
                )}
            </div>
        </FieldWrapper>
    );
};

export default Email;
