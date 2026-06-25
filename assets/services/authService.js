import api from '../axios';

export default {
    login(email, password) {
        return api.post('/login', { email, password });
    },

    getToken() {
        return localStorage.getItem('token');
    },

    getUser() {
        const stored = localStorage.getItem('user');
        return stored ? JSON.parse(stored) : null;
    },

    isLoggedIn() {
        return !!this.getToken();
    },

    setSession(token, user) {
        localStorage.setItem('token', token);
        localStorage.setItem('user', JSON.stringify(user));
    },

    logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
    },
};