import { Chess } from 'chess.js'
import { INPUT_EVENT_TYPE, COLOR, Chessboard, BORDER_TYPE, FEN } from 'cm-chessboard';
import { MARKER_TYPE, Markers } from 'cm-chessboard/src/extensions/markers/Markers.js';
import {PROMOTION_DIALOG_RESULT_TYPE, PromotionDialog} from "cm-chessboard/src/extensions/promotion-dialog/PromotionDialog.js"
import {Accessibility} from "cm-chessboard/src/extensions/accessibility/Accessibility.js"


window.Chess = Chess
window.Chessboard = Chessboard
window.FEN = FEN
window.Markers = Markers
window.MARKER_TYPE = MARKER_TYPE
window.INPUT_EVENT_TYPE = INPUT_EVENT_TYPE
window.COLOR = COLOR
window.BORDER_TYPE = BORDER_TYPE
window.PROMOTION_DIALOG_RESULT_TYPE = PROMOTION_DIALOG_RESULT_TYPE
window.PromotionDialog = PromotionDialog
window.Accessibility = Accessibility
