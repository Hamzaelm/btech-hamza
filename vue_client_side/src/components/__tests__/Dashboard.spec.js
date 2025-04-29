import { describe, it, expect, beforeEach, afterEach } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import DashboardView from '@/views/DashboardView.vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { jest } from 'globals'

jest.mock('axios')
jest.mock('sweetalert2')

const mockTasks = [
  { id: 1, title: 'Task 1', description: 'Description 1', completed: false },
  { id: 2, title: 'Task 2', description: 'Description 2', completed: true },
]

describe('DashboardView.vue', () => {
  beforeEach(() => {
    axios.get.mockResolvedValue({ data: mockTasks })
    Swal.fire.mockResolvedValue({ isConfirmed: true })
  })

  it('renders dashboard and fetches tasks', async () => {
    const wrapper = mount(DashboardView)
    expect(axios.get).toHaveBeenCalledWith('API_URL' + 'todos')
    await flushPromises()

    const taskTitles = wrapper.findAll('.task-title')
    expect(taskTitles.length).toBe(mockTasks.length)
    expect(taskTitles[0].text()).toBe('Task 1')
    expect(taskTitles[1].text()).toBe('Task 2')
  })

  it('opens and closes the add task modal', async () => {
    const wrapper = mount(DashboardView)
    await wrapper.find('button.btn-success').trigger('click')
    expect(wrapper.find('.modal-backdrop').exists()).toBe(true)
    await wrapper.find('.btn-secondary').trigger('click')
    expect(wrapper.find('.modal-backdrop').exists()).toBe(false)
  })

  it('completes a task when clicking the complete button', async () => {
    const wrapper = mount(DashboardView)
    await flushPromises()
    await wrapper.find('button.btn-light').trigger('click')
    expect(axios.patch).toHaveBeenCalledWith('API_URL' + 'todos/1', {}, expect.any(Object))
  })

  it('opens edit modal and edits a task', async () => {
    const wrapper = mount(DashboardView)
    await flushPromises()
    await wrapper.find('button.btn-warning').trigger('click')
    expect(wrapper.find('.modal-backdrop').exists()).toBe(true)
    expect(wrapper.find('input').element.value).toBe('Task 1')

    await wrapper.setData({
      editingTask: { title: 'Updated Task', description: 'Updated Description' },
    })
    await wrapper.find('.btn-success').trigger('click')
    expect(axios.put).toHaveBeenCalledWith(
      'API_URL' + 'todos/1',
      { title: 'Updated Task', description: 'Updated Description' },
      expect.any(Object),
    )
  })

  it('deletes a task', async () => {
    const wrapper = mount(DashboardView)
    await flushPromises()
    await wrapper.find('button.btn-danger').trigger('click')
    expect(axios.delete).toHaveBeenCalledWith('API_URL' + 'todos/1', expect.any(Object))
  })

  afterEach(() => {
    jest.clearAllMocks()
  })
})
