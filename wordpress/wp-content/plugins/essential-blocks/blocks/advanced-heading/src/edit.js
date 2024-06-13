/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";
import { useEffect } from "@wordpress/element";
import {
    BlockControls,
    AlignmentToolbar,
} from "@wordpress/block-editor";
import { select } from "@wordpress/data";
import { useEntityProp, store as coreStore } from '@wordpress/core-data';

/**
 * Internal depencencies
 */
import Inspector from "./inspector";

/**
 * External depencencies
 */
const {
    DynamicInputValueHandler,
    EBDisplayIcon,
    BlockProps
} = window.EBControls;

import Style from "./style";

export default function Edit(props) {
    const {
        attributes,
        setAttributes,
        isSelected,
    } = props;
    const {
        blockId,
        preset,
        align,
        tagName,
        titleText,
        subtitleTagName,
        subtitleText,
        displaySubtitle,
        displaySeperator,
        seperatorPosition,
        seperatorType,
        separatorIcon,
        classHook,
        source,
        currentPostId,
        currentPostType
    } = attributes;


    // you must declare this variable
    const enhancedProps = {
        ...props,
        blockPrefix: 'eb-advance-heading',
        style: <Style {...props} />
    };

    useEffect(() => {
        if(source == undefined) {
            setAttributes({source: 'custom'})
        }
    },[])

    useEffect(() => {
        const postId = select("core/editor")?.getCurrentPostId();
        const postType = select("core/editor")?.getCurrentPostType();

        if (postId) {
            setAttributes({
                currentPostId: postId,
                currentPostType: postType,
            });
        }
    }, [source])

    const [rawTitle = '', setTitle, fullTitle] = useEntityProp(
        'postType',
        currentPostType,
        'title',
        currentPostId
    );

    let TagName = tagName;

    return (
        <>
            {isSelected && (
                <>
                    <BlockControls>
                        <AlignmentToolbar
                            value={align}
                            onChange={(align) => setAttributes({ align })}
                            controls={["left", "center", "right"]}
                        />
                    </BlockControls>
                    <Inspector
                        attributes={attributes}
                        setAttributes={setAttributes}
                    />
                </>
            )}

            <BlockProps.Edit {...enhancedProps}>

                {source == 'dynamic-title' && currentPostId == 0 && (
                    <div className="eb-loading" >
                        <img src={`${EssentialBlocksLocalize?.image_url}/ajax-loader.gif`} alt="Loading..." />
                    </div >
                )}

                {((source == 'dynamic-title' && currentPostId != 0) || source == 'custom') && (
                    <>
                        <div
                            className={`eb-parent-wrapper eb-parent-${blockId} ${classHook}`}
                        >
                            <div
                                className={`eb-advance-heading-wrapper ${blockId} ${preset}`}
                                data-id={blockId}
                            >
                                {displaySeperator && seperatorPosition === "top" && (
                                    <div className={"eb-ah-separator " + seperatorType}>
                                        {seperatorType === "icon" && (
                                            // <i
                                            //     className={`${separatorIcon
                                            //         ? separatorIcon
                                            //         : "fas fa-arrow-circle-down"
                                            //         }`}
                                            // ></i>
                                            <EBDisplayIcon icon={separatorIcon} />
                                        )}
                                    </div>
                                )}

                                {source == 'dynamic-title' && (
                                    <>
                                        {currentPostId > 0 && (
                                            <DynamicInputValueHandler
                                                value={rawTitle}
                                                tagName={tagName}
                                                className="eb-ah-title"
                                                allowedFormats={[
                                                    "core/bold",
                                                    "core/italic",
                                                    "core/link",
                                                    "core/strikethrough",
                                                    "core/underline",
                                                    "core/text-color",
                                                ]}
                                                onChange={setTitle}
                                                readOnly={true}
                                            />
                                        )}

                                        {/* for FSE */}
                                        {typeof currentPostId == 'string' && (
                                            <TagName>
                                                {rawTitle ? rawTitle : __('Title')}
                                            </TagName>
                                        )}
                                    </>

                                )}

                                {source == 'custom' && (
                                    <DynamicInputValueHandler
                                        value={titleText}
                                        tagName={tagName}
                                        className="eb-ah-title"
                                        allowedFormats={[
                                            "core/bold",
                                            "core/italic",
                                            "core/link",
                                            "core/strikethrough",
                                            "core/underline",
                                            "core/text-color",
                                        ]}
                                        onChange={(titleText) =>
                                            setAttributes({ titleText })
                                        }
                                        readOnly={true}
                                    />
                                )}

                                {source == 'custom' && displaySubtitle && (
                                    <DynamicInputValueHandler
                                        tagName={subtitleTagName}
                                        className="eb-ah-subtitle"
                                        value={subtitleText}
                                        allowedFormats={[
                                            "core/bold",
                                            "core/italic",
                                            "core/link",
                                            "core/strikethrough",
                                            "core/underline",
                                            "core/text-color",
                                        ]}
                                        onChange={(subtitleText) =>
                                            setAttributes({ subtitleText })
                                        }
                                        readOnly={true}
                                    />
                                )}
                                {displaySeperator && seperatorPosition === "bottom" && (
                                    <div className={"eb-ah-separator " + seperatorType}>
                                        {seperatorType === "icon" && (
                                            // <i
                                            //     className={`${separatorIcon
                                            //         ? separatorIcon
                                            //         : "fas fa-arrow-circle-down"
                                            //         }`}
                                            // ></i>
                                            <EBDisplayIcon icon={separatorIcon} />
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>
                    </>
                )}
            </BlockProps.Edit>
        </>
    );
}
