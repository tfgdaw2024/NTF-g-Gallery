/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";
import {
    // InspectorControls,
    // MediaUpload,
    useBlockProps,
    // RichText,
    InnerBlocks,
} from "@wordpress/block-editor";
import classnames from "classnames";
import { select, useSelect } from "@wordpress/data";
const { BlockProps } = window.EBControls;
export default function Edit(props) {
    const { attributes, className, clientId } = props;
    const {
        //
        tabId,

        //
        tabParentId,
    } = attributes;

    // you must declare this variable
    const enhancedProps = {
        ...props,
        rootClass: `eb-guten-block-main-parent-wrapper eb-tab-editor-wrap`,
    };

    const { blocks } = useSelect((select) => ({
        blocks: select("core/block-editor").getBlockOrder(clientId)
    }), []);

    return (
        <BlockProps.Edit {...enhancedProps}>
            <div
                className={`eb-tab-wrapper`}
                data-tab-id={tabId}
                data-tab-parent-id={tabParentId}
            >
                <div className="eb-tab-inner">
                    {/* <h5>tabId: {tabId}</h5> */}
                    <InnerBlocks
                        orientation={"vertical"}
                        templateLock={
                            // templateLock ? templateLock :
                            false
                        }
                        renderAppender={
                            blocks.length > 0
                                ? undefined
                                : InnerBlocks.ButtonBlockAppender
                        }
                    />
                </div>
            </div>
        </BlockProps.Edit>
    );
}
