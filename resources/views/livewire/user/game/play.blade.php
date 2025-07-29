<div>
    <x-slot name="title">
        {{ __('jetadmin.game') }} #{{ $game->id }}
    </x-slot>

    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('jetadmin.game') }} #{{ $game->id }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('jetadmin.game_play_description') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Game Information -->
        <div class="space-y-4">
            <flux:card class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('jetadmin.game_information') }}</flux:heading>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="font-medium">{{ __('jetadmin.status') }}:</span>
                        @php
                            $status = __('jetadmin.not_started');
                            $statusColor = 'green';

                            if ($game->start_at && !$game->end_at) {
                                $status = __('jetadmin.in_progress');
                                $statusColor = 'red';
                            } elseif ($game->end_at) {
                                $status = __('jetadmin.ended');
                                $statusColor = 'yellow';
                            }
                        @endphp
                        <flux:badge :color="$statusColor" size="sm">{{ $status }}</flux:badge>
                    </div>

                    <div class="flex justify-between">
                        <span class="font-medium">{{ __('jetadmin.start_date') }}:</span>
                        <span>{{ $game->start_at ? $game->start_at->format('M d, Y H:i') : __('jetadmin.not_started') }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="font-medium">{{ __('jetadmin.end_date') }}:</span>
                        <span>{{ $game->end_at ? $game->end_at->format('M d, Y H:i') : '-' }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="font-medium">{{ __('jetadmin.your_color') }}:</span>
                        @php
                            $user = auth()->user();
                            $userRole = $game->getUserRole($user);
                        @endphp
                        <flux:badge color="solid" size="sm">{{ ucfirst(__('jetadmin.' . $userRole)) }}</flux:badge>
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Players Information -->
        <div class="space-y-4">
            <flux:card class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('jetadmin.players') }}</flux:heading>
                </div>
                <div class="space-y-4">
                    <!-- White Player -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center gap-3">
                            <flux:avatar size="sm" src="{{ $game->whitePlayer->profile_photo_url ?? '' }}" />
                            <div>
                                <div class="font-medium">{{ $game->whitePlayer->name }}</div>
                                <div class="text-sm text-gray-500">{{ $game->whitePlayer->email }}</div>
                            </div>
                        </div>
                        <flux:badge color="solid" size="sm">{{ __('jetadmin.white') }}</flux:badge>
                    </div>

                    <!-- Black Player -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center gap-3">
                            <flux:avatar size="sm" src="{{ $game->blackPlayer->profile_photo_url ?? '' }}" />
                            <div>
                                <div class="font-medium">{{ $game->blackPlayer->name }}</div>
                                <div class="text-sm text-gray-500">{{ $game->blackPlayer->email }}</div>
                            </div>
                        </div>
                        <flux:badge color="solid" size="sm">{{ __('jetadmin.black') }}</flux:badge>
                    </div>
                </div>
            </flux:card>
        </div>
    </div>

    <!-- Chess Board Placeholder -->
    <div class="mt-8">
        <flux:card class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('jetadmin.chess_board') }}</flux:heading>
            </div>
            <div class="flex items-center justify-center h-64 bg-gray-100 dark:bg-gray-800 rounded-lg">
                <div class="text-center">
                    <flux:icon name="chess" class="w-16 h-16 text-gray-400 mb-4" />
                    <p class="text-gray-500 dark:text-gray-400">{{ __('jetadmin.chess_board_coming_soon') }}</p>
                    <div wire:ignore class="board board-large" id="board"></div>
                </div>
            </div>
        </flux:card>
    </div>
</div>

@script
<script>
    const chess = new Chess()

    let seed = 71;
    function random() {
        const x = Math.sin(seed++) * 10000;
        return x - Math.floor(x);
    }
    function makeEngineMove(chessboard) {
        const possibleMoves = chess.moves({verbose: true})
        if (possibleMoves.length > 0) {
            const randomIndex = Math.floor(random() * possibleMoves.length)
            const randomMove = possibleMoves[randomIndex]
            setTimeout(() => { // smoother with 500ms delay
                chess.move({from: randomMove.from, to: randomMove.to})
                chessboard.setPosition(chess.fen(), true)
                chessboard.enableMoveInput(inputHandler, COLOR.white)
            }, 500)
        }
    }

    function inputHandler(event) {
        console.log("inputHandler", event)
        if(event.type === INPUT_EVENT_TYPE.movingOverSquare) {
            return // ignore this event
        }
        if(event.type !== INPUT_EVENT_TYPE.moveInputFinished) {
            event.chessboard.removeLegalMovesMarkers()
        }
        if (event.type === INPUT_EVENT_TYPE.moveInputStarted) {
            // mark legal moves
            const moves = chess.moves({square: event.squareFrom, verbose: true})
            event.chessboard.addLegalMovesMarkers(moves)
            return moves.length > 0
        } else if (event.type === INPUT_EVENT_TYPE.validateMoveInput) {
            $wire.dispatch('handleMoveData');
            const move = {from: event.squareFrom, to: event.squareTo, promotion: event.promotion}
            const result = chess.move(move)
            if (result) {
                event.chessboard.state.moveInputProcess.then(() => { // wait for the move input process has finished
                    event.chessboard.setPosition(chess.fen(), true).then(() => { // update position, maybe castled and wait for animation has finished
                        makeEngineMove(event.chessboard)
                    })
                })
            } else {
                // promotion?
                let possibleMoves = chess.moves({square: event.squareFrom, verbose: true})
                for (const possibleMove of possibleMoves) {
                    if (possibleMove.promotion && possibleMove.to === event.squareTo) {
                        event.chessboard.showPromotionDialog(event.squareTo, COLOR.white, (result) => {
                            console.log("promotion result", result)
                            if (result.type === PROMOTION_DIALOG_RESULT_TYPE.pieceSelected) {
                                chess.move({from: event.squareFrom, to: event.squareTo, promotion: result.piece.charAt(1)})
                                event.chessboard.setPosition(chess.fen(), true)
                                makeEngineMove(event.chessboard)
                            } else {
                                // promotion canceled
                                event.chessboard.enableMoveInput(inputHandler, COLOR.white)
                                event.chessboard.setPosition(chess.fen(), true)
                            }
                        })
                        return true
                    }
                }
            }
            return result
        } else if (event.type === INPUT_EVENT_TYPE.moveInputFinished) {
            if(event.legalMove) {
                event.chessboard.disableMoveInput()
            }
        }
    }

    const board = new Chessboard(document.getElementById("board"), {
        position: chess.fen(),
        assetsUrl: "{{ url('assets') }}/",
        style: {borderType: BORDER_TYPE.none, pieces: {file: "{{ url('assets/pieces/staunty.svg') }}"}, animationDuration: 300},
        orientation: COLOR.white,
        extensions: [
            {class: Markers, props: {autoMarkers: MARKER_TYPE.square}},
            {class: PromotionDialog},
            {class: Accessibility, props: {visuallyHidden: true}}
        ]
    })
    board.enableMoveInput(inputHandler, COLOR.white)
</script>
@endscript
