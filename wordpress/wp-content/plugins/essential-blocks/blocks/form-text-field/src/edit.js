/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";
import { useBlockProps, RichText, InnerBlocks } from "@wordpress/block-editor";
import { useEffect, useState, useRef } from "@wordpress/element";
import { select, dispatch, useSelect } from "@wordpress/data";

/**
 * Internal dependencies
 */

const {
    duplicateBlockIdFix,
    filterBlocksByName,
    getBlockParentClientId,
    EBDisplayIcon,
    DynamicInputValueHandler,
    BlockProps
} = EBControls;

import classnames from "classnames";
import Inspector from "./inspector";
import Style from "./style";

export default function Edit(props) {
    const {
        attributes,
        setAttributes,
        isSelected,
        clientId,
        className,
        name,
    } = props;
    const {
        resOption,
        blockMeta,
        parentBlockId,
        blockId,
        classHook,
        showLabel,
        labelText,
        fieldName,
        defaultValue,
        placeholderText,
        isRequired,
        isHidden,
        validationMessage,
        validationRules,
        isIcon,
        icon,
        formStyle,
        parentIconColor,
        parentBlockPaddingLeft,
        parentBlockPaddingUnit,
        parentBlockIconSize,
    } = attributes;

    let parentFormStyle = "";

    // you must declare this variable
    const enhancedProps = {
        ...props,
        blockPrefix: 'eb-text-field',
        rootClass: `eb-guten-block-main-parent-wrapper eb-form-field`,
        style: <Style {...props} />
    };

    //UseEffect for set Validation rules
    useEffect(() => {
        const rules = {
            [fieldName]: {
                isRequired: {
                    status: isRequired,
                    message: validationMessage,
                },
            },
        };
        setAttributes({ validationRules: rules });
    }, [isRequired, fieldName, validationMessage]);

    return (
        <>
            {isSelected && (
                <Inspector
                    clientId={clientId}
                    attributes={attributes}
                    setAttributes={setAttributes}
                />
            )}
            <BlockProps.Edit {...enhancedProps}>
                <div
                    className={`eb-parent-wrapper eb-parent-${blockId} ${classHook}`}
                >
                    <div
                        className={`${blockId} eb-text-field-wrapper eb-field-wrapper`}
                    >
                        {showLabel && formStyle != "form-style-modern" && (
                            <>
                                <label htmlFor={fieldName}>
                                    <DynamicInputValueHandler
                                        value={labelText}
                                        onChange={(labelText) =>
                                            setAttributes({ labelText })
                                        }
                                        readOnly={true}
                                    />{" "}
                                    {isRequired && (
                                        <span className="eb-required">*</span>
                                    )}
                                </label>
                            </>
                        )}
                        <div className="eb-field-input-wrap">
                            {isIcon && icon && <EBDisplayIcon icon={icon} className={"eb-input-icon"} />}
                            <input
                                type={isHidden ? "hidden" : "text"}
                                id={fieldName}
                                name={fieldName}
                                className={"eb-field-input"}
                                value={defaultValue}
                                placeholder={placeholderText}
                                required={isRequired}
                            />

                            {formStyle == "form-style-modern" && (
                                <>
                                    <label htmlFor={fieldName}>
                                        {labelText}{" "}
                                        {isRequired && (
                                            <span className="eb-required">
                                                *
                                            </span>
                                        )}
                                    </label>
                                </>
                            )}
                        </div>
                        {isRequired && (
                            <>
                                <div
                                    className={`eb-form-validation eb-validate-${fieldName}`}
                                >
                                    {validationMessage}
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </BlockProps.Edit>
        </>
    );
}
