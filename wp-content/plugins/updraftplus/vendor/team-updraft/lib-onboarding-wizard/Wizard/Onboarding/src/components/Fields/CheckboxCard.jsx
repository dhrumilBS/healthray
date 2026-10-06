import { useEffect, useState } from "react";
import * as RadixAccordion from "@radix-ui/react-accordion";
import FieldWrapper from "@/components/Fields/FieldWrapper";
import CheckboxInput from "@/components/Fields/CheckboxInput";
import SwitchInput from "@/components/Fields/SwitchInput";
import { __ } from "@wordpress/i18n";
import Icon from "@/utils/Icon";
import { renderPossiblyHtml } from "../../utils/html";

const CheckboxCard = ({
    field,
    onChange,
    value,
}) => {
    const [expanded, setExpanded] = useState(false);

    const disabled = field.is_lock === true;

    // Mirror legacy Checkbox behavior: when locked, force value to false.
    useEffect(() => {
        if (disabled && value !== false) {
            onChange(false);
        }
    }, [disabled]);

    const hasDetails = typeof field.details === "string" && field.details.trim() !== "";
    const hasSummary = typeof field.summary === "string" && field.summary.trim() !== "";

    const showDetailsLabel = field.show_details_label || __("Show details", "ONBOARDING_WIZARD_TEXT_DOMAIN");
    const hideDetailsLabel = field.hide_details_label || __("Hide details", "ONBOARDING_WIZARD_TEXT_DOMAIN");

    const Toggle = field.subtype === "switch" ? SwitchInput : CheckboxInput;

    const textColor = disabled ? "text-[#9FA4A7]" : "text-gray-1000";

    return (
        <FieldWrapper
            label={""}
            inputId={field.id}
            className="pb-2 !mt-1 border-b border-grey last:border-b-0"
        >
            <div className="flex items-start gap-2">
                {field.icon && (
                    <div className="flex-shrink-0">
                        <Icon name={field.icon} size={24} />
                    </div>
                )}

                <div className="flex-1 min-w-0">
                    <label
                       htmlFor={field.id}
                       className={`font-semibold text-md block cursor-pointer ${textColor}`}
                    >
                        {field.label}
                    </label>

                    {hasSummary && (
                        <div className={`text-md font-normal ${textColor}`}>
                            {renderPossiblyHtml(field.summary)}
                        </div>
                    )}

                    {hasDetails && (
                        <RadixAccordion.Root
                            type="single"
                            value={expanded ? "details" : ""}
                            onValueChange={(v) => setExpanded(v === "details")}
                            collapsible
                        >
                            <RadixAccordion.Item value="details">
                                <RadixAccordion.Header className="flex">
                                    <RadixAccordion.Trigger
                                        className="mt-1 text-sm font-medium flex items-center gap-1 text-[var(--teamupdraft-grey-700)] hover:text-[var(--teamupdraft-grey-900)] focus:outline-none focus:ring-0 bg-transparent border-0 p-0 cursor-pointer"
                                    >
                                        <span>{expanded ? hideDetailsLabel : showDetailsLabel}</span>
                                        <span
                                            className={`inline-flex transition-transform duration-200 ${expanded ? "rotate-180" : ""}`}
                                            aria-hidden="true"
                                        >
                                            <Icon name="expand" size={16} color="gray500" fill="gray500" />
                                        </span>
                                    </RadixAccordion.Trigger>
                                </RadixAccordion.Header>
                                <RadixAccordion.Content
                                    className="overflow-hidden data-[state=open]:animate-slideDown data-[state=closed]:animate-slideUp"
                                >
                                    <div className={`text-sm font-normal italic mt-1 ${textColor}`}>
                                        {renderPossiblyHtml(field.details)}
                                    </div>
                                </RadixAccordion.Content>
                            </RadixAccordion.Item>
                        </RadixAccordion.Root>
                    )}
                </div>

                <div className="flex-shrink-0">
                    <Toggle
                        label={field.label}
                        onChange={(checked) => !disabled && onChange(!!checked)}
                        value={disabled ? false : !!value}
                        id={field.id}
                        disabled={disabled}
                    />
                </div>
            </div>
        </FieldWrapper>
    );
};

export default CheckboxCard;
