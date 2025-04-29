import { describe, it, expect, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import LoginView from '@/views/LoginView.vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { jest } from 'globals'
import { API_URL } from '@/services/apis'
// Mocking axios and Swal
jest.mock('axios')
jest.mock('sweetalert2', () => ({
  fire: jest.fn(),
}))

describe('LoginView.vue', () => {
  let wrapper

  beforeEach(() => {
    wrapper = mount(LoginView)
  })

  it('renders login form correctly', () => {
    expect(wrapper.find('h2').text()).toBe('BTECH-TODOLIST')
    expect(wrapper.find('input#username').exists()).toBe(true)
    expect(wrapper.find('input#password').exists()).toBe(true)
  })

  it('submits login form successfully', async () => {
    // Mocking successful login response
    axios.post.mockResolvedValue({
      data: {
        token: 'fake-token',
      },
    })

    await wrapper.setData({
      username: 'testuser',
      password: 'password123',
    })

    await wrapper.find('form').trigger('submit.prevent')

    expect(axios.post).toHaveBeenCalledWith(API_URL + 'login', {
      username: 'testuser',
      password: 'password123',
    })
    expect(localStorage.setItem).toHaveBeenCalledWith('token', 'fake-token')
    expect(localStorage.setItem).toHaveBeenCalledWith('isConnected', 1)
  })

  it('shows an error message on login failure', async () => {
    // Mocking failed login response
    axios.post.mockRejectedValue({
      response: {
        data: {
          message: 'Invalid credentials',
        },
      },
    })

    await wrapper.setData({
      username: 'testuser',
      password: 'wrongpassword',
    })

    await wrapper.find('form').trigger('submit.prevent')

    expect(wrapper.vm.apiError).toBe('Invalid credentials')
  })

  it('renders the register modal when clicking "Inscrivez-vous"', async () => {
    await wrapper.find('a').trigger('click')
    expect(wrapper.vm.showRegisterModal).toBe(true)
  })

  it('submits register form successfully', async () => {
    // Mocking successful registration response
    axios.post.mockResolvedValue({
      data: {
        message: 'User registered successfully',
      },
    })

    await wrapper.setData({
      registerUsername: 'newuser',
      registerPassword: 'newpassword123',
      registerConfirmPassword: 'newpassword123',
    })

    await wrapper.find('.btn-success').trigger('click')

    expect(axios.post).toHaveBeenCalledWith(API_URL + 'register', {
      username: 'newuser',
      password: 'newpassword123',
    })
    expect(Swal.fire).toHaveBeenCalledWith('Bienvenue !')
    expect(wrapper.vm.showRegisterModal).toBe(false)
  })

  it('disables register button if passwords do not match', async () => {
    await wrapper.setData({
      registerPassword: 'password123',
      registerConfirmPassword: 'password456',
    })
    expect(wrapper.vm.registerPasswordMismatch).toBe(true)
    expect(wrapper.find('.btn-success').attributes('disabled')).toBe('disabled')
  })
})
