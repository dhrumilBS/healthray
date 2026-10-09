import { useEffect, useRef } from '@wordpress/element';

export const Modal = ( {
	isOpen,
	title,
	children,
	onConfirm,
	onClose,
	confirmText = 'Confirm',
	cancelText = 'Cancel',
	showFooter = true,
} ) => {
	const modalRef = useRef( null );

	useEffect( () => {
		const handleClickOutside = ( event ) => {
			if (
				modalRef.current &&
				! modalRef.current.contains( event.target )
			) {
				onClose();
			}
		};

		const handleEscapeKey = ( event ) => {
			if ( event.key === 'Escape' ) {
				onClose();
			}
		};

		if ( isOpen ) {
			document.addEventListener( 'mousedown', handleClickOutside );
			document.addEventListener( 'keydown', handleEscapeKey );
			document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
		}

		return () => {
			document.removeEventListener( 'mousedown', handleClickOutside );
			document.removeEventListener( 'keydown', handleEscapeKey );
			document.body.style.overflow = 'auto'; // Restore scrolling when modal is closed
		};
	}, [ isOpen, onClose ] );

	if ( ! isOpen ) return null;

	return (
		<div className="rcf7-modal__overlay">
			<div className="rcf7-modal__container" ref={ modalRef }>
				<div className="rcf7-modal__header">
					<h3>{ title }</h3>
				</div>

				<div className="rcf7-modal__content">{ children }</div>

				{ showFooter && (
					<div className="rcf7-modal__footer">
						<button
							className="rcf7-btn rcf7-btn--red"
							onClick={ onClose }
						>
							{ cancelText }
						</button>
						<button
							className="rcf7-btn rcf7-btn--blue"
							onClick={ onConfirm }
						>
							{ confirmText }
						</button>
					</div>
				) }
			</div>
		</div>
	);
};
