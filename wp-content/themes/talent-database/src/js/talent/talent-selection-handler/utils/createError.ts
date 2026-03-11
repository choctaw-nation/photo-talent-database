import { ToastType } from '../../../utils/types';

export default function createError( type: ToastType, message: string ): Error {
	const error = new Error( message );
	( error as any ).type = type;
	return error;
}
